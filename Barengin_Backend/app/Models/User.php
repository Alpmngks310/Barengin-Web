<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'adult_confirmed_at',
        'terms_accepted_at',
        'privacy_accepted_at',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'adult_confirmed_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'privacy_accepted_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'user_interests', 'user_id', 'category_id')
            ->withPivot('created_at');
    }

    public function activitiesHosted(): HasMany
    {
        return $this->hasMany(Activity::class, 'host_id');
    }

    public function joinRequests(): HasMany
    {
        return $this->hasMany(JoinRequest::class, 'user_id');
    }

    public function reviewedJoinRequests(): HasMany
    {
        return $this->hasMany(JoinRequest::class, 'reviewed_by');
    }

    public function activityParticipations(): HasMany
    {
        return $this->hasMany(ActivityParticipant::class, 'user_id');
    }

    public function favoriteActivities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'activity_favorites', 'user_id', 'activity_id')
            ->withPivot('created_at');
    }

    public function blockedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'blocker_id', 'blocked_user_id')
            ->withPivot('created_at');
    }

    public function blockedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'blocked_user_id', 'blocker_id')
            ->withPivot('created_at');
    }

    public function reportsMade(): HasMany
    {
        return $this->hasMany(UserReport::class, 'reporter_id');
    }

    public function reportsReceived(): HasMany
    {
        return $this->hasMany(UserReport::class, 'reported_user_id');
    }
}
