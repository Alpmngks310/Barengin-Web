<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DecideJoinRequestRequest;
use App\Http\Requests\Api\StoreJoinRequestRequest;
use App\Http\Resources\JoinRequestResource;
use App\Models\Activity;
use App\Models\JoinRequest;
use App\Services\JoinRequestService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class JoinRequestController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly JoinRequestService $service) {}

    public function store(StoreJoinRequestRequest $request, Activity $activity)
    {
        $joinRequest = $this->service->submit(
            $activity,
            $request->user(),
            $request->validated('message')
        );

        return $this->success(
            JoinRequestResource::make($joinRequest)->resolve($request),
            'Permintaan bergabung berhasil dikirim.',
            201
        );
    }

    public function mine(Request $request)
    {
        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $paginator = JoinRequest::query()
            ->where('user_id', $request->user()->id)
            ->with(['activity', 'user.profile'])->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($validated['per_page'] ?? 12);

        $items = $paginator->getCollection()->map(
            fn(JoinRequest $joinRequest) => JoinRequestResource::make($joinRequest)->resolve($request)
        );

        return $this->success(
            $items,
            'Daftar permintaan saya berhasil dimuat.',
            200,
            $this->paginationMeta($paginator)
        );
    }

    public function cancelPending(Request $request, JoinRequest $joinRequest)
    {
        $this->service->cancelPending($joinRequest, $request->user());

        return $this->success(null, 'Permintaan bergabung berhasil dibatalkan.');
    }

    public function forActivity(Request $request, Activity $activity)
    {
        $this->authorize('manageRequests', $activity);

        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $paginator = $activity->joinRequests()
            ->with(['user.profile', 'activity'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($validated['per_page'] ?? 20);

        $items = $paginator->getCollection()->map(
            fn(JoinRequest $joinRequest) => JoinRequestResource::make($joinRequest)->resolve($request)
        );

        return $this->success(
            $items,
            'Permintaan untuk aktivitas berhasil dimuat.',
            200,
            $this->paginationMeta($paginator)
        );
    }

    public function decide(DecideJoinRequestRequest $request, JoinRequest $joinRequest)
    {
        $data = $request->validated();

        $result = $this->service->decide(
            $joinRequest,
            $request->user(),
            $data['status'],
            $data['decision_note'] ?? null
        );

        return $this->success(
            JoinRequestResource::make($result)->resolve($request),
            $data['status'] === 'accepted' ? 'Permintaan disetujui.' : 'Permintaan ditolak.'
        );
    }
}
