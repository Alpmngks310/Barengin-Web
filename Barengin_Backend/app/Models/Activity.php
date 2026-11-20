<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    protected $fillable = [
        'host_id',
        'category_id',
        'title',
        'description',
        'participant_level',
        'starts_at',
        'ends_at',
        'timezone',
        'venue_name',
        'address',
        'area',
        'city',
        'province',
        'country_code',
        'latitude',
        'longitude',
        'capacity',
        'budget_amount',
        'currency',
        'cover_image_path',
        'status',
        'published_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'published_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'capacity' => 'integer',
            'budget_amount' => 'integer',
        ];
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function joinRequests(): HasMany
    {
        return $this->hasMany(JoinRequest::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ActivityParticipant::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'activity_favorites', 'activity_id', 'user_id')
            ->withPivot('created_at');
    }

    public function joinedParticipants(): HasMany
    {
        return $this->hasMany(ActivityParticipant::class)->where('status', 'joined');
    }
}
