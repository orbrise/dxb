<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'category_id',
        'author_id',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'views',
        'is_featured',
        'allow_comments',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'allow_comments' => 'boolean',
    ];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            
            // Auto-generate excerpt if not provided
            if (empty($post->excerpt) && !empty($post->content)) {
                $post->excerpt = Str::limit(strip_tags($post->content), 500);
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title') && !$post->isDirty('slug')) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    /**
     * Get the category of the post
     */
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    /**
     * Get the author of the post
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the tags for the post
     */
    public function tags()
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag', 'post_id', 'tag_id');
    }

    /**
     * Scope for published posts
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->where('published_at', '<=', Carbon::now());
    }

    /**
     * Scope for featured posts
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for draft posts
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Check if post is published
     */
    public function isPublished()
    {
        return $this->status === 'published' && 
               $this->published_at && 
               $this->published_at <= Carbon::now();
    }

    /**
     * Get formatted published date
     */
    public function getFormattedDateAttribute()
    {
        return $this->published_at ? $this->published_at->format('M d, Y') : null;
    }

    /**
     * Get reading time in minutes
     */
    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / 200); // Average reading speed
        return max(1, $minutes);
    }

    /**
     * Increment view count
     */
    public function incrementViews()
    {
        $this->increment('views');
    }

    /**
     * Get the featured image URL
     */
    public function getFeaturedImageUrlAttribute()
    {
        if ($this->featured_image) {
            // Check if it's a full URL
            if (filter_var($this->featured_image, FILTER_VALIDATE_URL)) {
                return $this->featured_image; 
            }
            
            // Always use CDN path for blog images
            $cdnDomain = 'https://assets.massagerepublic.com.co/blogs';
            
            // Remove any path prefix like 'blog/images/' if present (legacy data)
            $filename = basename($this->featured_image);
            
            return $cdnDomain . '/' . $filename;
        }
        return 'https://assets.massagerepublic.com.co/blogs/placeholder.jpg';
    }

    /**
     * Get SEO title
     */
    public function getSeoTitleAttribute()
    {
        return $this->meta_title ?: $this->title;
    }

    /**
     * Get SEO description
     */
    public function getSeoDescriptionAttribute()
    {
        return $this->meta_description ?: $this->excerpt;
    }
}
