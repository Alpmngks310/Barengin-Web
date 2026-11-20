<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\ActivityParticipant;
use App\Models\JoinRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JoinRequestService
{

    public function submit(Activity $activity, User $user, ?string $message): JoinRequest
    {
        return DB::transaction(function () use ($activity, $user, $message) {
            $lockedActivity = Activity::query()
                ->whereKey($activity->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Host tidak boleh mengajukan permintaan ke aktivitas sendiri.
            if ((int) $lockedActivity->host_id === (int) $user->id) {
                throw ValidationException::withMessages([
                    'activity_id' => [
                        'Host tidak dapat mengajukan permintaan ke aktivitas miliknya sendiri.'
                    ],
                ]);
            }

            // Aktivitas harus dipublikasikan dan belum dimulai.
            if (
                $lockedActivity->status !== 'published'
                || ! $lockedActivity->starts_at
                || ! $lockedActivity->starts_at->isFuture()
            ) {
                throw ValidationException::withMessages([
                    'activity_id' => [
                        'Aktivitas ini tidak menerima permintaan bergabung.'
                    ],
                ]);
            }

            // Periksa pembatasan keamanan antar akun.
            $hasBlock = $user->blockedUsers()
                ->whereKey($lockedActivity->host_id)
                ->exists()
                || $user->blockedByUsers()
                ->whereKey($lockedActivity->host_id)
                ->exists();

            if ($hasBlock) {
                throw ValidationException::withMessages([
                    'activity_id' => [
                        'Permintaan tidak dapat dikirim karena ada pembatasan keamanan antara akun.'
                    ],
                ]);
            }

            // Periksa riwayat pengajuan.
            // Semua status dianggap pernah mengajukan.
            $previousRequest = JoinRequest::query()
                ->where('activity_id', $lockedActivity->id)
                ->where('user_id', $user->id)
                ->latest('id')
                ->first();

            if ($previousRequest) {
                $message = match ($previousRequest->status) {
                    'rejected' => 'Permintaan bergabung kamu sebelumnya telah ditolak.',
                    'cancelled' => 'Permintaan bergabung kamu sebelumnya telah dibatalkan.',
                    'accepted' => 'Kamu sudah pernah diterima dalam aktivitas ini.',
                    'pending' => 'Kamu sudah memiliki permintaan bergabung yang sedang diproses.',
                    default => 'Kamu sudah pernah mengajukan permintaan untuk aktivitas ini.',
                };

                throw ValidationException::withMessages([
                    'activity_id' => [$message],
                ]);
            }

            // Periksa status kepesertaan.
            $membership = ActivityParticipant::query()
                ->where('activity_id', $lockedActivity->id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if ($membership && $membership->status === 'joined') {
                throw ValidationException::withMessages([
                    'activity_id' => [
                        'Kamu sudah menjadi peserta aktivitas ini.'
                    ],
                ]);
            }

            if ($membership && $membership->status === 'removed') {
                throw ValidationException::withMessages([
                    'activity_id' => [
                        'Kamu telah dikeluarkan dari aktivitas ini oleh Host dan tidak dapat mendaftar kembali.'
                    ],
                ]);
            }

            // Periksa kapasitas aktivitas.
            $joinedCount = $lockedActivity->participants()
                ->where('status', 'joined')
                ->count();

            if ($joinedCount >= $lockedActivity->capacity) {
                throw ValidationException::withMessages([
                    'activity_id' => [
                        'Kapasitas aktivitas sudah penuh.'
                    ],
                ]);
            }

            // Buat pengajuan baru jika seluruh pemeriksaan berhasil.
            $request = $lockedActivity->joinRequests()->create([
                'user_id' => $user->id,
                'message' => $message,
                'status' => 'pending',
            ]);

            return $request->fresh(['activity', 'user.profile']);
        }, 3);
    }


    public function cancelPending(JoinRequest $joinRequest, User $member): void
    {
        DB::transaction(function () use ($joinRequest, $member) {
            // Urutan lock: Activity terlebih dahulu, kemudian JoinRequest.
            Activity::query()
                ->whereKey($joinRequest->activity_id)
                ->lockForUpdate()
                ->firstOrFail();

            $request = JoinRequest::query()
                ->whereKey($joinRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $request->user_id !== (int) $member->id) {
                abort(403, 'Kamu hanya dapat membatalkan permintaan milik sendiri.');
            }

            // Pembatalan berulang bersifat idempotent.
            if ($request->status === 'cancelled') {
                return;
            }

            if ($request->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => [
                        'Hanya permintaan berstatus pending yang dapat dibatalkan.'
                    ],
                ]);
            }

            // Simpan riwayat pembatalan, jangan hapus record.
            $request->update([
                'status' => 'cancelled',
            ]);
        }, 3);
    }

    public function decide(
        JoinRequest $joinRequest,
        User $host,
        string $status,
        ?string $note
    ): JoinRequest {
        return DB::transaction(function () use (
            $joinRequest,
            $host,
            $status,
            $note
        ) {
            // Urutan lock: Activity terlebih dahulu, kemudian JoinRequest.
            $activity = Activity::query()
                ->whereKey($joinRequest->activity_id)
                ->lockForUpdate()
                ->firstOrFail();

            $request = JoinRequest::query()
                ->whereKey($joinRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $activity->host_id !== (int) $host->id) {
                abort(403, 'Hanya Host aktivitas yang dapat memutuskan permintaan.');
            }

            if ($request->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => [
                        'Permintaan ini sudah diproses sebelumnya.'
                    ],
                ]);
            }

            if ($status === 'accepted') {
                if (
                    $activity->status !== 'published'
                    || ! $activity->starts_at->isFuture()
                ) {
                    throw ValidationException::withMessages([
                        'status' => [
                            'Aktivitas tidak dapat menerima peserta baru.'
                        ],
                    ]);
                }

                $hasBlock = $host->blockedUsers()
                    ->whereKey($request->user_id)
                    ->exists()
                    || $host->blockedByUsers()
                    ->whereKey($request->user_id)
                    ->exists();

                if ($hasBlock) {
                    throw ValidationException::withMessages([
                        'status' => [
                            'Permintaan tidak dapat disetujui karena ada pembatasan keamanan.'
                        ],
                    ]);
                }

                $joinedCount = $activity->participants()
                    ->where('status', 'joined')
                    ->count();

                if ($joinedCount >= $activity->capacity) {
                    throw ValidationException::withMessages([
                        'status' => [
                            'Kapasitas aktivitas sudah penuh.'
                        ],
                    ]);
                }

                $participant = ActivityParticipant::query()
                    ->where('activity_id', $activity->id)
                    ->where('user_id', $request->user_id)
                    ->lockForUpdate()
                    ->first();

                if ($participant && $participant->status === 'removed') {
                    throw ValidationException::withMessages([
                        'status' => [
                            'Pengguna yang telah dikeluarkan tidak dapat bergabung kembali.'
                        ],
                    ]);
                }

                if ($participant && $participant->status === 'joined') {
                    throw ValidationException::withMessages([
                        'status' => [
                            'Pengguna sudah menjadi peserta aktivitas ini.'
                        ],
                    ]);
                }

                if ($participant) {
                    // Mengaktifkan kembali keanggotaan yang sebelumnya left.
                    $participant->update([
                        'role' => 'member',
                        'status' => 'joined',
                        'joined_at' => now(),
                        'left_at' => null,
                        'removed_by' => null,
                        'removal_note' => null,
                    ]);
                } else {
                    $activity->participants()->create([
                        'user_id' => $request->user_id,
                        'role' => 'member',
                        'status' => 'joined',
                        'joined_at' => now(),
                    ]);
                }
            }

            // Riwayat keputusan disimpan pada record pengajuan terkait.
            $request->update([
                'status' => $status,
                'reviewed_by' => $host->id,
                'reviewed_at' => now(),
                'decision_note' => $note,
            ]);

            return $request->fresh([
                'activity',
                'user.profile',
                'reviewer'
            ]);
        }, 3);
    }
}
