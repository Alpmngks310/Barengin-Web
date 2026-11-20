<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreReportRequest;
use App\Models\User;
use App\Models\UserReport;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class SafetyController extends Controller
{
    use ApiResponse;

    public function report(StoreReportRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        if (empty($data['reported_user_id']) && empty($data['activity_id'])) {
            return $this->failure('Laporan harus memiliki target pengguna atau aktivitas.', 422);
        }

        if (
            ! empty($data['reported_user_id'])
            && (int) $data['reported_user_id'] === (int) $user->id
        ) {
            return $this->failure('Kamu tidak dapat melaporkan akun sendiri.', 422);
        }

        $report = UserReport::create(array_merge($data, [
            'reporter_id' => $user->id,
            'status' => 'pending',
        ]));

        return $this->success([
            'id' => $report->id,
            'status' => $report->status,
            'created_at' => $report->created_at?->toISOString(),
        ], 'Laporan berhasil dikirim.', 201);
    }

    public function block(Request $request, User $user)
    {
        abort_if(
            (int) $user->id === (int) $request->user()->id,
            422,
            'Kamu tidak dapat memblokir akun sendiri.'
        );
        $request->user()->blockedUsers()->syncWithoutDetaching([$user->id]);

        return $this->success(['blocked_user_id' => $user->id], 'Pengguna berhasil diblokir.');
    }

    public function unblock(Request $request, User $user)
    {
        $request->user()->blockedUsers()->detach($user->id);

        return $this->success(['blocked_user_id' => $user->id], 'Blokir pengguna berhasil dibatalkan.');
    }

    public function blockedUsers(Request $request)
    {
        $users = $request->user()->blockedUsers()
            ->select('users.id', 'users.name', 'users.username')
            ->orderBy('users.name')
            ->get();

        return $this->success($users, 'Daftar blokir berhasil dimuat.');
    }
}
