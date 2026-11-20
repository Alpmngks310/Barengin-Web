<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreActivityRequest;
use App\Http\Requests\Api\UpdateActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Models\ActivityParticipant;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ActivityController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'category_slug' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:120'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'budget_max' => ['nullable', 'integer', 'min:0'],
            'participant_level' => ['nullable', 'in:beginner,all_levels,experienced'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'radius_km' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $user = Auth::guard('sanctum')->user();
        $query = Activity::query()
            ->where('status', 'published')
            ->where('starts_at', '>', now())
            ->with(['host.profile', 'category'])
            ->withCount(['participants as joined_participants_count' => fn($q) => $q->where('status', 'joined')]);

        if ($user) {
            $query
                ->whereDoesntHave('host.blockedUsers', fn($q) => $q->whereKey($user->id))
                ->whereDoesntHave('host.blockedByUsers', fn($q) => $q->whereKey($user->id));
            $query->withExists([
                'favoritedBy as is_favorited' => fn($q) => $q->where('users.id', $user->id),
            ]);
        }

        if (! empty($validated['q'])) {
            $term = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $validated['q']) . '%';
            $query->where(fn($q) => $q
                ->where('title', 'like', $term)
                ->orWhere('description', 'like', $term)
                ->orWhere('venue_name', 'like', $term)
                ->orWhere('city', 'like', $term));
        }

        if (! empty($validated['category_id'])) {
            $query->where('category_id', $validated['category_id']);
        }

        if (! empty($validated['category_slug'])) {
            $query->whereHas('category', fn($q) => $q->where('slug', $validated['category_slug']));
        }

        foreach (['city', 'area'] as $field) {
            if (! empty($validated[$field])) {
                $query->where($field, 'like', '%' . $validated[$field] . '%');
            }
        }

        if (! empty($validated['date_from'])) {
            $query->whereDate('starts_at', '>=', $validated['date_from']);
        }
        if (! empty($validated['date_to'])) {
            $query->whereDate('starts_at', '<=', $validated['date_to']);
        }
        if (array_key_exists('budget_max', $validated)) {
            $query->where('budget_amount', '<=', $validated['budget_max']);
        }
        if (! empty($validated['participant_level'])) {
            $query->where('participant_level', $validated['participant_level']);
        }

        $hasCoordinates = isset($validated['latitude'], $validated['longitude']);
        if ($hasCoordinates) {
            $lat = (float) $validated['latitude'];
            $lng = (float) $validated['longitude'];
            $radius = (float) ($validated['radius_km'] ?? 25);

            $distanceSql = '(6371 * ACOS(LEAST(1, GREATEST(-1,
                COS(RADIANS(?)) * COS(RADIANS(latitude)) *
                COS(RADIANS(longitude) - RADIANS(?)) +
                SIN(RADIANS(?)) * SIN(RADIANS(latitude))
            ))))';

            $query->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->selectRaw("$distanceSql AS distance_km", [$lat, $lng, $lat])
                ->whereRaw("$distanceSql <= ?", [$lat, $lng, $lat, $radius])
                ->orderBy('distance_km')
                ->orderBy('starts_at')
                ->orderBy('id');
        } else {
            $query->orderBy('starts_at')
                ->orderBy('id');
        }

        $paginator = $query->paginate($validated['per_page'] ?? 12);
        $items = $paginator->getCollection()->map(
            fn(Activity $activity) => ActivityResource::make($activity)->resolve($request)
        );

        return $this->success($items, 'Aktivitas berhasil dimuat.', 200, $this->paginationMeta($paginator));
    }

    public function mine(Request $request)
    {
        $validated = $request->validate([
            'role' => ['nullable', 'in:host,member,all'],
            'status' => ['nullable', 'in:draft,published,ongoing,completed,cancelled'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $user = $request->user();
        $role = $validated['role'] ?? 'all';

        $query = Activity::query()
            ->with(['host.profile', 'category'])
            ->withCount(['participants as joined_participants_count' => fn($q) => $q->where('status', 'joined')])
            ->where(function ($q) use ($user, $role) {
                if ($role === 'host' || $role === 'all') {
                    $q->orWhere('host_id', $user->id);
                }
                if ($role === 'member' || $role === 'all') {
                    $q->orWhereHas('participants', fn($p) => $p
                        ->where('user_id', $user->id)
                        ->where('role', 'member')
                        ->where('status', 'joined'));
                }
            });

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $paginator = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($validated['per_page'] ?? 12);
        $items = $paginator->getCollection()->map(
            fn(Activity $activity) => ActivityResource::make($activity)->resolve($request)
        );

        return $this->success($items, 'Aktivitas saya berhasil dimuat.', 200, $this->paginationMeta($paginator));
    }

    public function show(Request $request, Activity $activity)
    {
        /** @var \App\Models\User|null $viewer */
        $viewer = Auth::guard('sanctum')->user();
        if (
            $activity->status !== 'published'
            && (
                ! $viewer
                || (int) $activity->host_id !== (int) $viewer->id
            )
        ) {
            abort(404);
        }

        if ($viewer && (int) $activity->host_id !== (int) $viewer->id) {
            $hasBlock = $viewer->blockedUsers()->whereKey($activity->host_id)->exists()
                || $viewer->blockedByUsers()->whereKey($activity->host_id)->exists();

            if ($hasBlock) {
                abort(404);
            }
        }

        $activity->load(['host.profile', 'category'])
            ->loadCount(['participants as joined_participants_count' => fn($q) => $q->where('status', 'joined')]);

        if ($viewer) {
            $activity->loadExists([
                'favoritedBy as is_favorited' => fn($q) => $q->where('users.id', $viewer->id),
            ]);
        }

        return $this->success(ActivityResource::make($activity)->resolve($request), 'Detail aktivitas berhasil dimuat.');
    }

    public function store(StoreActivityRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        $activity = DB::transaction(function () use ($data, $user) {
            $activity = Activity::create(array_merge($data, [
                'host_id' => $user->id,
                'timezone' => $data['timezone'] ?? 'Asia/Jakarta',
                'country_code' => $data['country_code'] ?? 'ID',
                'budget_amount' => $data['budget_amount'] ?? 0,
                'currency' => $data['currency'] ?? 'IDR',
                'status' => 'draft',
            ]));

            // Capacity includes the Host, therefore Host is participant #1.
            $activity->participants()->create([
                'user_id' => $user->id,
                'role' => 'host',
                'status' => 'joined',
                'joined_at' => now(),
            ]);

            return $activity;
        });

        $activity->load(['host.profile', 'category'])
            ->loadCount(['participants as joined_participants_count' => fn($q) => $q->where('status', 'joined')]);

        return $this->success(ActivityResource::make($activity)->resolve($request), 'Aktivitas berhasil disimpan sebagai draft.', 201);
    }

    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        $this->authorize('update', $activity);
        $data = $request->validated();
        $user = $request->user();

        $updated = DB::transaction(function () use ($activity, $data, $user) {
            $locked = Activity::query()->whereKey($activity->id)->lockForUpdate()->firstOrFail();

            abort_unless((int) $locked->host_id === (int) $user->id, 403);

            if (! in_array($locked->status, ['draft', 'published'], true) || ! $locked->starts_at->isFuture()) {
                throw ValidationException::withMessages([
                    'activity' => ['Aktivitas ini sudah tidak dapat diubah.'],
                ]);
            }

            $effectiveStart = $data['starts_at'] ?? $locked->starts_at;
            $effectiveEnd = $data['ends_at'] ?? $locked->ends_at;

            if (\Illuminate\Support\Carbon::parse($effectiveEnd)->lessThanOrEqualTo(\Illuminate\Support\Carbon::parse($effectiveStart))) {
                throw ValidationException::withMessages([
                    'ends_at' => ['Waktu selesai harus setelah waktu mulai.'],
                ]);
            }

            if (isset($data['capacity'])) {
                $joinedCount = $locked->participants()->where('status', 'joined')->count();
                if ($data['capacity'] < $joinedCount) {
                    throw ValidationException::withMessages([
                        'capacity' => ['Kapasitas tidak boleh lebih kecil daripada jumlah peserta yang sudah bergabung.'],
                    ]);
                }
            }

            $locked->update($data);

            return $locked;
        }, 3);

        $updated->refresh()->load(['host.profile', 'category'])
            ->loadCount(['participants as joined_participants_count' => fn($q) => $q->where('status', 'joined')]);

        return $this->success(ActivityResource::make($updated)->resolve($request), 'Aktivitas berhasil diperbarui.');
    }
    public function publish(Request $request, Activity $activity)
    {
        $this->authorize('publish', $activity);
        $user = $request->user();

        $published = DB::transaction(function () use ($activity, $user) {
            $locked = Activity::query()->whereKey($activity->id)->lockForUpdate()->firstOrFail();

            abort_unless((int) $locked->host_id === (int) $user->id, 403);
            if ($locked->status !== 'draft') {
                throw ValidationException::withMessages([
                    'status' => ['Hanya draft yang dapat dipublikasikan.'],
                ]);
            }

            if (! $locked->starts_at->isFuture()) {
                throw ValidationException::withMessages([
                    'starts_at' => ['Aktivitas harus dimulai pada waktu mendatang.'],
                ]);
            }

            $locked->update([
                'status' => 'published',
                'published_at' => now(),
            ]);

            return $locked;
        }, 3);

        $published->load(['host.profile', 'category'])
            ->loadCount(['participants as joined_participants_count' => fn($q) => $q->where('status', 'joined')]);

        return $this->success(ActivityResource::make($published)->resolve($request), 'Aktivitas berhasil dipublikasikan.');
    }
    public function cancel(Request $request, Activity $activity)
    {
        $this->authorize('cancel', $activity);
        $data = $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ]);
        $user = $request->user();

        DB::transaction(function () use ($activity, $data, $user) {
            $locked = Activity::query()->whereKey($activity->id)->lockForUpdate()->firstOrFail();

            abort_unless((int) $locked->host_id === (int) $user->id, 403);
            if (! in_array($locked->status, ['draft', 'published', 'ongoing'], true)) {
                throw ValidationException::withMessages([
                    'status' => ['Aktivitas ini tidak dapat dibatalkan.'],
                ]);
            }

            $locked->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $data['cancellation_reason'] ?? null,
            ]);

            $locked->joinRequests()
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'reviewed_by' => $user->id,
                    'reviewed_at' => now(),
                    'decision_note' => 'Aktivitas dibatalkan oleh Host.',
                    'updated_at' => now(),
                ]);
        }, 3);

        return $this->success(null, 'Aktivitas berhasil dibatalkan.');
    }
    public function leave(Request $request, Activity $activity)
    {
        $user = $request->user();

        DB::transaction(function () use ($activity, $user) {
            $locked = Activity::query()->whereKey($activity->id)->lockForUpdate()->firstOrFail();

            if ((int) $locked->host_id === (int) $user->id) {
                throw ValidationException::withMessages([
                    'membership' => ['Host tidak dapat keluar dari aktivitas miliknya sendiri.'],
                ]);
            }

            $participant = $locked->participants()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $participant) {
                throw ValidationException::withMessages([
                    'membership' => ['Kamu bukan peserta dari aktivitas ini.'],
                ]);
            }

            if ($participant->role === 'host') {
                throw ValidationException::withMessages([
                    'membership' => ['Host tidak dapat keluar dari aktivitas miliknya sendiri.'],
                ]);
            }

            if ($participant->status === 'left') {
                return;
            }

            if ($participant->status === 'removed') {
                throw ValidationException::withMessages([
                    'membership' => ['Kamu telah dikeluarkan dari aktivitas ini oleh Host.'],
                ]);
            }

            if ($participant->status !== 'joined') {
                throw ValidationException::withMessages([
                    'membership' => ['Kamu tidak terdaftar sebagai peserta aktif.'],
                ]);
            }

            $participant->update([
                'status' => 'left',
                'left_at' => now(),
            ]);
        }, 3);

        return $this->success(null, 'Kamu sudah keluar dari aktivitas.');
    }

    public function start(Request $request, Activity $activity)
    {
        $user = $request->user();

        DB::transaction(function () use ($activity, $user) {
            $locked = Activity::query()->whereKey($activity->id)->lockForUpdate()->firstOrFail();

            abort_unless((int) $locked->host_id === (int) $user->id, 403);
            if ($locked->status !== 'published') {
                throw ValidationException::withMessages([
                    'status' => ['Hanya aktivitas Published yang dapat dimulai.'],
                ]);
            }

            if ($locked->starts_at->isFuture() || $locked->ends_at->isPast()) {
                throw ValidationException::withMessages([
                    'status' => ['Aktivitas hanya dapat dimulai pada rentang waktu pelaksanaannya.'],
                ]);
            }

            $locked->update(['status' => 'ongoing']);

            $locked->joinRequests()
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'reviewed_by' => $user->id,
                    'reviewed_at' => now(),
                    'decision_note' => 'Aktivitas sudah dimulai dan tidak menerima permintaan baru.',
                    'updated_at' => now(),
                ]);
        }, 3);

        return $this->success(null, 'Aktivitas sekarang berstatus ongoing.');
    }
    public function participants(Request $request, Activity $activity)
    {
        $this->authorize('manageRequests', $activity);

        $participants = $activity->participants()
            ->where('status', 'joined')
            ->with(['user.profile'])
            ->orderByRaw("CASE WHEN role = 'host' THEN 0 ELSE 1 END")
            ->orderBy('joined_at')
            ->get()
            ->map(fn($participant) => [
                'id' => $participant->id,
                'user_id' => $participant->user_id,
                'role' => $participant->role,
                'status' => $participant->status,
                'joined_at' => $participant->joined_at?->toISOString(),
                'user' => [
                    'id' => $participant->user->id,
                    'name' => $participant->user->name,
                    'username' => $participant->user->username,
                    'profile' => $participant->user->profile ? [
                        'display_name' => $participant->user->profile->display_name,
                        'avatar_path' => $participant->user->profile->avatar_path,
                        'city' => $participant->user->profile->city,
                    ] : null,
                ],
            ]);

        return $this->success($participants, 'Daftar peserta berhasil dimuat.');
    }



    public function removeParticipant(
        Request $request,
        Activity $activity,
        int $userId
    ) {
        $actor = $request->user();

        // Host tidak boleh mengeluarkan dirinya sendiri.
        if (
            (int) $activity->host_id === (int) $actor->id
            && (int) $userId === (int) $actor->id
        ) {
            return $this->failure(
                'Host tidak dapat mengeluarkan diri sendiri dari aktivitas.',
                422
            );
        }

        // Hanya Host yang dapat mengelola peserta.
        $this->authorize('manageRequests', $activity);

        $data = $request->validate([
            'removal_note' => ['nullable', 'string', 'max:500'],
        ]);

        $result = DB::transaction(function () use (
            $activity,
            $userId,
            $data,
            $actor
        ) {
            $locked = Activity::query()
                ->whereKey($activity->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                (int) $locked->host_id === (int) $actor->id,
                403
            );

            $participant = $locked->participants()
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            // Peserta tidak ditemukan.
            if (! $participant) {
                return 'not_found';
            }

            // Host tidak dapat dikeluarkan.
            if (
                $participant->role === 'host'
                || (int) $participant->user_id === (int) $actor->id
            ) {
                return 'invalid_host';
            }

            // Hanya peserta aktif yang dapat dikeluarkan.
            if ($participant->status !== 'joined') {
                return 'conflict';
            }

            $note = $data['removal_note'] ?? 'Dikeluarkan oleh Host.';

            $participant->update([
                'status' => 'removed',
                'left_at' => now(),
                'removed_by' => $actor->id,
                'removal_note' => $note,
            ]);

            // Ambil JoinRequest terbaru tanpa mengubah
            // status historis keputusan Host.
            $joinRequest = $locked->joinRequests()
                ->where('user_id', $userId)
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if ($joinRequest) {
                $joinRequest->update([
                    'decision_note' => $note,
                ]);
            }

            return 'removed';
        }, 3);

        if ($result === 'not_found') {
            return $this->failure(
                'Peserta tidak ditemukan dalam aktivitas ini.',
                404
            );
        }

        if ($result === 'invalid_host') {
            return $this->failure(
                'Host tidak dapat mengeluarkan dirinya sendiri.',
                422
            );
        }

        if ($result === 'conflict') {
            return $this->failure(
                'Peserta sudah tidak aktif dalam aktivitas ini.',
                409
            );
        }

        return $this->success(
            null,
            'Peserta berhasil dikeluarkan dari aktivitas.'
        );
    }



    public function complete(Request $request, Activity $activity)
    {
        $user = $request->user();

        DB::transaction(function () use ($activity, $user) {
            $locked = Activity::query()->whereKey($activity->id)->lockForUpdate()->firstOrFail();

            abort_unless((int) $locked->host_id === (int) $user->id, 403);
            if ($locked->status !== 'ongoing') {
                throw ValidationException::withMessages([
                    'status' => ['Hanya aktivitas yang sedang berlangsung yang dapat diselesaikan.'],
                ]);
            }

            $locked->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $locked->joinRequests()
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'reviewed_by' => $user->id,
                    'reviewed_at' => now(),
                    'decision_note' => 'Aktivitas telah selesai.',
                    'updated_at' => now(),
                ]);
        }, 3);

        return $this->success(null, 'Aktivitas ditandai selesai.');
    }
}
