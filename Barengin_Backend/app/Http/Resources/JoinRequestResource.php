<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JoinRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'activity_id' => $this->activity_id,

            'user' => $this->whenLoaded('user', function ($user) {
                if (! $user) {
                    return null;
                }

                if (! $user) {
                    return null;
                }

                $profile = $user->relationLoaded('profile')
                    ? $user->profile
                    : null;

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'profile' => $profile ? [
                        'display_name' => $profile->display_name,
                        'avatar_path' => $profile->avatar_path,
                        'city' => $profile->city,
                    ] : null,
                ];
            }),

            'activity' => $this->whenLoaded('activity', function ($activity) {
                if (! $activity) {
                    return null;
                }

                if (! $activity) {
                    return null;
                }

                return [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'starts_at' => $activity->starts_at?->toIso8601String(),
                    'city' => $activity->city,
                    'status' => $activity->status,
                ];
            }),

            'message' => $this->message,
            'status' => $this->status,
            'reviewed_by' => $this->reviewed_by,
            'reviewed_at' => $this->reviewed_at?->toISOString(),
            'decision_note' => $this->decision_note,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
