<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'participant_level' => $this->participant_level,

            'starts_at' => $this->starts_at?->toIso8601String(),
            'ends_at' => $this->ends_at?->toIso8601String(),
            'timezone' => $this->timezone,

            'venue' => [
                'name' => $this->venue_name,
                'address' => $this->address,
                'area' => $this->area,
                'city' => $this->city,
                'province' => $this->province,
                'country_code' => $this->country_code,
                'latitude' => $this->latitude !== null
                    ? (float) $this->latitude
                    : null,
                'longitude' => $this->longitude !== null
                    ? (float) $this->longitude
                    : null,
            ],

            'capacity' => (int) $this->capacity,
            'joined_count' => (int) ($this->joined_participants_count ?? 0),
            'available_slots' => max(
                0,
                (int) $this->capacity
                    - (int) ($this->joined_participants_count ?? 0)
            ),

            'budget' => [
                'amount' => (int) $this->budget_amount,
                'currency' => $this->currency,
            ],

            'cover_image_path' => $this->cover_image_path,
            'cover_image_url' => $this->cover_image_path
                ? $this->getStorageUrl($this->cover_image_path)
                : null,

            'status' => $this->status,
            'is_favorited' => (bool) ($this->is_favorited ?? false),

            'distance_km' => isset($this->distance_km)
                ? round((float) $this->distance_km, 2)
                : null,

            'host' => $this->whenLoaded('host', function ($host) {
                if (! $host) {
                    return null;
                }

                $profile = $host->relationLoaded('profile')
                    ? $host->profile
                    : null;

                return [
                    'id' => $host->id,
                    'name' => $host->name,
                    'username' => $host->username,
                    'profile' => $profile ? [
                        'display_name' => $profile->display_name,
                        'avatar_path' => $profile->avatar_path,
                        'avatar_url' => $profile->avatar_path
                            ? $this->getStorageUrl($profile->avatar_path)
                            : null,
                        'city' => $profile->city,
                    ] : null,
                ];
            }),

            'category' => $this->whenLoaded('category', function ($category) {
                if (! $category) {
                    return null;
                }

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'icon_key' => $category->icon_key,
                ];
            }),

            'published_at' => $this->published_at?->toISOString(),
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
