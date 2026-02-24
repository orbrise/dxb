<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * Get the posts for the tag
     */
    public function posts()
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_tag', 'tag_id', 'post_id');
    }

    /**
     * Get published posts count
     */
    public function getPublishedPostsCountAttribute()
    {
        return $this->posts()->where('status', 'published')->count();
    }
}
