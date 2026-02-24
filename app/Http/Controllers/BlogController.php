<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
 
class BlogController extends Controller
{
    /**
     * Get sidebar data for all views
     */
    private function getSidebarData()
    {
        $categories = BlogCategory::active()
            ->withCount(['posts' => function($query) {
                $query->where('status', 'published');
            }])
            ->ordered()
            ->get();
        
        $popularTags = BlogTag::withCount(['posts' => function($query) {
                $query->where('status', 'published');
            }])
            ->having('posts_count', '>', 0)
            ->orderByDesc('posts_count')
            ->take(15)
            ->get();
        
        // Get archive data grouped by year and month
        $archiveData = $this->getArchiveData();
        
        return compact('categories', 'popularTags', 'archiveData');
    }

    /**
     * Get archive data grouped by year and month with post titles
     */
    private function getArchiveData()
    {
        $posts = BlogPost::published()
            ->select('id', 'title', 'slug', 'published_at')
            ->orderByDesc('published_at')
            ->get();

        $archive = [];
        
        foreach ($posts as $post) {
            $year = $post->published_at->format('Y');
            $month = $post->published_at->format('n'); // 1-12
            $monthName = $post->published_at->format('F'); // Full month name
            
            if (!isset($archive[$year])) {
                $archive[$year] = [
                    'count' => 0,
                    'months' => []
                ];
            }
            
            if (!isset($archive[$year]['months'][$month])) {
                $archive[$year]['months'][$month] = [
                    'name' => $monthName,
                    'count' => 0,
                    'posts' => []
                ];
            }
            
            $archive[$year]['count']++;
            $archive[$year]['months'][$month]['count']++;
            $archive[$year]['months'][$month]['posts'][] = [
                'title' => $post->title,
                'slug' => $post->slug
            ];
        }
        
        // Sort years descending, months descending
        krsort($archive);
        foreach ($archive as &$yearData) {
            krsort($yearData['months']);
        }
        
        return $archive;
    }

    /**
     * Display the blog homepage
     */
    public function index(Request $request)
    {
        $posts = BlogPost::with(['category', 'author'])
            ->published()
            ->latest('published_at')
            ->paginate(10);
        
        $featuredPosts = BlogPost::with(['category', 'author'])
            ->published()
            ->featured()
            ->latest('published_at')
            ->take(3)
            ->get();
        
        $sidebarData = $this->getSidebarData();
        
        return view('blog.index', array_merge(
            compact('posts', 'featuredPosts'),
            $sidebarData
        ));
    }

    /**
     * Display a single blog post
     */
    public function show($slug)
    {
        $post = BlogPost::with(['category', 'author', 'tags'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();
        
        // Increment view count
        $post->incrementViews();
        
        // Get related posts from same category
        $relatedPosts = BlogPost::with(['category'])
            ->published()
            ->where('id', '!=', $post->id)
            ->where('category_id', $post->category_id)
            ->latest('published_at')
            ->take(3)
            ->get();
        
        // If not enough related posts, get recent posts
        if ($relatedPosts->count() < 3) {
            $additionalPosts = BlogPost::with(['category'])
                ->published()
                ->where('id', '!=', $post->id)
                ->whereNotIn('id', $relatedPosts->pluck('id'))
                ->latest('published_at')
                ->take(3 - $relatedPosts->count())
                ->get();
            
            $relatedPosts = $relatedPosts->concat($additionalPosts);
        }
        
        $sidebarData = $this->getSidebarData();
        
        return view('blog.show', array_merge(
            compact('post', 'relatedPosts'),
            $sidebarData
        ));
    }

    /**
     * Display posts by category
     */
    public function category($slug)
    {
        $category = BlogCategory::where('slug', $slug)
            ->active()
            ->firstOrFail();
        
        $posts = BlogPost::with(['category', 'author'])
            ->where('category_id', $category->id)
            ->published()
            ->latest('published_at')
            ->paginate(10);
        
        $sidebarData = $this->getSidebarData();
        
        return view('blog.category', array_merge(
            compact('category', 'posts'),
            $sidebarData
        ));
    }

    /**
     * Display posts by tag
     */
    public function tag($slug)
    {
        $tag = BlogTag::where('slug', $slug)->firstOrFail();
        
        $posts = $tag->posts()
            ->with(['category', 'author'])
            ->published()
            ->latest('published_at')
            ->paginate(10);
        
        $sidebarData = $this->getSidebarData();
        
        return view('blog.tag', array_merge(
            compact('tag', 'posts'),
            $sidebarData
        ));
    }

    /**
     * Search blog posts
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        $posts = BlogPost::with(['category', 'author'])
            ->published()
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%");
            })
            ->latest('published_at')
            ->paginate(10);
        
        $sidebarData = $this->getSidebarData();
        
        return view('blog.search', array_merge(
            compact('posts', 'query'),
            $sidebarData
        ));
    }

    /**
     * Display posts by archive (year/month)
     */
    public function archive($year, $month = null)
    {
        $query = BlogPost::with(['category', 'author'])
            ->published()
            ->whereYear('published_at', $year);
        
        if ($month) {
            $query->whereMonth('published_at', $month);
            $monthName = \Carbon\Carbon::create()->month($month)->format('F');
            $title = "{$monthName} {$year}";
        } else {
            $title = $year;
        }
        
        $posts = $query->latest('published_at')->paginate(10);
        
        $sidebarData = $this->getSidebarData();
        
        return view('blog.archive', array_merge(
            compact('posts', 'year', 'month', 'title'),
            $sidebarData
        ));
    }
}
