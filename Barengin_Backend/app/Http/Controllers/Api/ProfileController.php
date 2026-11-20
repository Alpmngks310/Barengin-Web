<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateInterestsRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use ApiResponse;

    public function show(Request $request)
    {
        $user = $request->user()->load(['profile', 'interests']);

        return $this->success(UserResource::make($user)->resolve($request), 'Profil berhasil dimuat.');
    }


    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        $oldPath = $user->profile?->avatar_path;
        $newPath = null;

        try {
            DB::transaction(function () use (
                $user,
                $data,
                $request,
                &$newPath
            ) {
                $userFields = [];

                foreach (['name', 'username'] as $field) {
                    if (array_key_exists($field, $data)) {
                        $userFields[$field] = $data[$field];
                    }
                }

                if ($userFields !== []) {
                    $user->update($userFields);
                }

                $profileFields = [];

                foreach (
                    [
                        'display_name',
                        'bio',
                        'area',
                        'city',
                        'province',
                        'country_code',
                    ] as $field
                ) {
                    if (array_key_exists($field, $data)) {
                        $profileFields[$field] = $data[$field];
                    }
                }

                if ($request->hasFile('avatar')) {
                    $newPath = $request->file('avatar')
                        ->store('avatars', 'public');

                    if ($newPath === false) {
                        throw new \RuntimeException(
                            'Gagal menyimpan avatar baru.'
                        );
                    }

                    $profileFields['avatar_path'] = $newPath;
                }

                if ($profileFields !== []) {
                    $profile = $user->profile()->firstOrNew(
                        ['user_id' => $user->id]
                    );

                    if (! $profile->exists && ! isset($profileFields['display_name'])) {
                        $profileFields['display_name'] = $user->name;
                    }

                    $profile->fill($profileFields);
                    $profile->save();
                }
            });
        } catch (\Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }

            throw $exception;
        }

        // Hapus avatar lama hanya setelah transaksi berhasil.
        if ($newPath && $oldPath && $oldPath !== $newPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $user->refresh()->load(['profile', 'interests']);

        return $this->success(
            UserResource::make($user)->resolve($request),
            'Profil berhasil diperbarui.'
        );
    }


    public function updateInterests(UpdateInterestsRequest $request)
    {
        $user = $request->user();
        $ids = array_values(array_unique($request->validated('category_ids')));

        $activeCount = DB::table('categories')
            ->whereIn('id', $ids)
            ->where('is_active', 1)
            ->count();

        if ($activeCount !== count($ids)) {
            return $this->failure('Satu atau lebih kategori tidak aktif atau tidak tersedia.', 422);
        }

        DB::transaction(function () use ($user, $ids) {
            DB::table('user_interests')->where('user_id', $user->id)->delete();

            $now = now();
            DB::table('user_interests')->insert(array_map(
                fn($id) => [
                    'user_id' => $user->id,
                    'category_id' => $id,
                    'created_at' => $now,
                ],
                $ids
            ));
        });

        $user->load(['profile', 'interests']);

        return $this->success(
            UserResource::make($user)->resolve($request),
            'Minat berhasil diperbarui.'
        );
    }
}
