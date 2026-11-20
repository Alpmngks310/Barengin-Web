<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;


class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,

            'profile' => $this->whenLoaded('profile', function ($profile) {
                if (! $profile) {
                    return null;
                }

                return [
                    'display_name' => $profile->display_name,
                    'bio' => $profile->bio,
                    'avatar_path' => $profile->avatar_path,
                    'avatar_url' => $profile->avatar_path
                        ? $this->getStorageUrl($profile->avatar_path)
                        : null,
                    'area' => $profile->area,
                    'city' => $profile->city,
                    'province' => $profile->province,
                    'country_code' => $profile->country_code,
                ];
            }),

            'interests' => $this->whenLoaded(
                'interests',
                fn() => $this->interests->map(fn($category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'icon_key' => $category->icon_key,
                ])->values()
            ),

            'created_at' => $this->created_at?->toISOString(),
        ];
    }
    private function getStorageUrl(string $path): string

    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $disk->url($path);
    }
}
