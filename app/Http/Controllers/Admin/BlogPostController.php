<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BlogPostController extends Controller
{
    /**
     * Display a listing of blog posts
     */
    public function index(Request $request)
    {
        $allowed = [10, 25, 50, 100];
        $perPage = (int) $request->get('perPage', 10);
        if (!in_array($perPage, $allowed)) {
            $perPage = 10;
        }

        $query = BlogPost::with(['category', 'author']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->latest()
            ->paginate($perPage)
            ->withQueryString();

        $categories = BlogCategory::active()->ordered()->get();
        
        return view('admin.blog.posts.index', compact('posts', 'categories'));
    }

    /**
     * Show the form for creating a new post
     */
    public function create()
    {
        $categories = BlogCategory::active()->ordered()->get();
        $tags = BlogTag::orderBy('name')->get();
        
        return view('admin.blog.posts.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created post
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:blog_categories,id',
            'status' => 'required|in:draft,published,scheduled',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:blog_tags,id',
        ]);

        // Handle slug
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        
        // Ensure unique slug
        $originalSlug = $validated['slug'];
        $count = 1;
        while (BlogPost::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            
            // Upload to external assets storage (CDN)
            $externalDisk = Storage::disk('assets_external');
            
            // Create blogs directory if it doesn't exist
            if (!$externalDisk->exists('blogs')) { 
                $externalDisk->makeDirectory('blogs');
            }
            
            // Store the file
            $externalDisk->put('blogs/' . $fileName, file_get_contents($file->getRealPath()));
            
            // Store just the filename (the model accessor will build the full URL)
            $validated['featured_image'] = $fileName;
        }

        // Set author
        $validated['author_id'] = Auth::id();

        // Handle checkboxes
        $validated['is_featured'] = $request->has('is_featured');
        $validated['allow_comments'] = $request->has('allow_comments');

        // Handle published_at for published posts
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = Carbon::now();
        }

        // Remove tags from validated data before creating
        $tags = $validated['tags'] ?? [];
        unset($validated['tags']);

        $post = BlogPost::create($validated);

        // Attach tags
        if (!empty($tags)) {
            $post->tags()->attach($tags);
        }

        return redirect()->route('admin.blog.posts.index')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified post
     */
    public function show(BlogPost $post)
    {
        $post->load(['category', 'author', 'tags']);
        return view('admin.blog.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the post
     */
    public function edit(BlogPost $post)
    {
        $categories = BlogCategory::active()->ordered()->get();
        $tags = BlogTag::orderBy('name')->get();
        $selectedTags = $post->tags->pluck('id')->toArray();
        
        return view('admin.blog.posts.edit', compact('post', 'categories', 'tags', 'selectedTags'));
    }

    /**
     * Update the specified post
     */
    public function update(Request $request, BlogPost $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug,' . $post->id,
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:blog_categories,id',
            'status' => 'required|in:draft,published,scheduled',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:blog_tags,id',
        ]);

        // Handle slug
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        
        // Ensure unique slug (excluding current post)
        $originalSlug = $validated['slug'];
        $count = 1;
        while (BlogPost::where('slug', $validated['slug'])->where('id', '!=', $post->id)->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image from external storage
            if ($post->featured_image) {
                $externalDisk = Storage::disk('assets_external');
                $externalDisk->delete('blogs/' . $post->featured_image);
            }
            
            $file = $request->file('featured_image');
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            
            // Upload to external assets storage (CDN)
            $externalDisk = Storage::disk('assets_external');
            
            // Create blogs directory if it doesn't exist
            if (!$externalDisk->exists('blogs')) {
                $externalDisk->makeDirectory('blogs');
            }
            
            // Store the file
            $externalDisk->put('blogs/' . $fileName, file_get_contents($file->getRealPath()));
            
            // Store just the filename (the model accessor will build the full URL)
            $validated['featured_image'] = $fileName;
        }

        // Handle checkboxes
        $validated['is_featured'] = $request->has('is_featured');
        $validated['allow_comments'] = $request->has('allow_comments');

        // Handle published_at for newly published posts
        if ($validated['status'] === 'published' && $post->status !== 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = Carbon::now();
        }

        // Remove tags from validated data before updating
        $tags = $validated['tags'] ?? [];
        unset($validated['tags']);

        $post->update($validated);

        // Sync tags
        $post->tags()->sync($tags);

        return redirect()->route('admin.blog.posts.index')
            ->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified post
     */
    public function destroy(BlogPost $post)
    {
        // Delete featured image from external storage
        if ($post->featured_image) {
            $externalDisk = Storage::disk('assets_external');
            $externalDisk->delete('blogs/' . $post->featured_image);
        }

        // Detach tags
        $post->tags()->detach();

        $post->delete();

        return redirect()->route('admin.blog.posts.index')
            ->with('success', 'Post deleted successfully.');
    }

    /**
     * Remove featured image via AJAX
     */
    public function removeImage(BlogPost $post)
    {
        if ($post->featured_image) {
            $externalDisk = Storage::disk('assets_external');
            $externalDisk->delete('blogs/' . $post->featured_image);
            $post->update(['featured_image' => null]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Quick status update via AJAX
     */
    public function updateStatus(Request $request, BlogPost $post)
    {
        $request->validate([
            'status' => 'required|in:draft,published,scheduled'
        ]);

        $updateData = ['status' => $request->status];
        
        if ($request->status === 'published' && $post->status !== 'published') {
            $updateData['published_at'] = Carbon::now();
        }

        $post->update($updateData);

        return response()->json(['success' => true, 'status' => $post->status]);
    }
}
