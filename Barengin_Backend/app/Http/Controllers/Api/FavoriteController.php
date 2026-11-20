<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    use ApiResponse;


    public function index(Request $request)
    {
        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $user = $request->user();

        $paginator = $user->favoriteActivities()
            ->where('activities.status', 'published')
            ->whereDoesntHave(
                'host.blockedUsers',
                fn($query) => $query->whereKey($user->id)
            )
            ->whereDoesntHave(
                'host.blockedByUsers',
                fn($query) => $query->whereKey($user->id)
            )
            ->with(['host.profile', 'category'])
            ->withCount([
                'participants as joined_participants_count' =>
                fn($query) => $query->where('status', 'joined'),
            ])
            ->orderByDesc('activity_favorites.created_at')
            ->orderByDesc('activities.id')
            ->paginate($validated['per_page'] ?? 12);

        $paginator->getCollection()->each(
            fn(Activity $activity) =>
            $activity->setAttribute('is_favorited', true)
        );

        $items = $paginator->getCollection()->map(
            fn(Activity $activity) =>
            ActivityResource::make($activity)->resolve($request)
        );

        return $this->success(
            $items,
            'Aktivitas favorit berhasil dimuat.',
            200,
            $this->paginationMeta($paginator)
        );
    }



    public function store(Request $request, Activity $activity)
    {
        $user = $request->user();

        if ($activity->status !== 'published') {
            return $this->failure(
                'Hanya aktivitas yang dipublikasikan yang dapat disimpan.',
                422
            );
        }

        $host = $activity->host;

        $isBlocked = ! $host
            || $host->blockedUsers()->whereKey($user->id)->exists()
            || $host->blockedByUsers()->whereKey($user->id)->exists();

        if ($isBlocked) {
            return $this->failure(
                'Aktivitas tidak dapat disimpan.',
                403
            );
        }

        $user->favoriteActivities()->syncWithoutDetaching([
            $activity->id => ['created_at' => now()],
        ]);

        return $this->success(
            ['is_favorited' => true],
            'Aktivitas ditambahkan ke favorit.'
        );
    }

    public function destroy(Request $request, Activity $activity)
    {
        $request->user()->favoriteActivities()->detach($activity->id);

        return $this->success(['is_favorited' => false], 'Aktivitas dihapus dari favorit.');
    }
}
