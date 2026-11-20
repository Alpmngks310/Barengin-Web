<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    public function view(User $user, Activity $activity): bool
    {
        return $activity->status === 'published'
            || (int) $activity->host_id === (int) $user->id;
    }

    public function update(User $user, Activity $activity): bool
    {
        return (int) $activity->host_id === (int) $user->id
            && in_array($activity->status, ['draft', 'published'], true)
            && $activity->starts_at->isFuture();
    }

    public function publish(User $user, Activity $activity): bool
    {
        return (int) $activity->host_id === (int) $user->id
            && $activity->status === 'draft';
    }

    public function cancel(User $user, Activity $activity): bool
    {
        return (int) $activity->host_id === (int) $user->id
            && in_array($activity->status, ['draft', 'published', 'ongoing'], true);
    }

    public function manageRequests(User $user, Activity $activity): bool
    {
        return (int) $activity->host_id === (int) $user->id;
    }
}
