<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorialVideo extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'title',
        'description',
        'video_id',
        'duration',
        'thumbnail_url',
        'view_count',
        'like_count',
        'is_published',
        'published_at',
        'tags',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'view_count' => 0,
        'like_count' => 0,
        'is_published' => false,
        'tags' => '[]',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'view_count' => 'integer',
        'like_count' => 'integer',
        'duration' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope a query to only include published videos.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->where(function($q) {
                        $q->whereNull('published_at')
                          ->orWhere('published_at', '<=', now());
                    });
    }

    /**
     * Scope a query to only include popular videos.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('view_count', 'desc')
                    ->take($limit);
    }

    /**
     * Scope a query to only include trending videos (most viewed in the last week).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTrending($query, $limit = 10)
    {
        return $query->where('created_at', '>=', now()->subWeek())
                    ->orderBy('view_count', 'desc')
                    ->take($limit);
    }
}
