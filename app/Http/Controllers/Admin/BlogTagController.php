<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogTagController extends Controller
{
    /**
     * Display a listing of blog tags
     */
    public function index(Request $request)
    {
        $allowed = [10, 25, 50, 100];
        $perPage = (int) $request->get('perPage', 10);
        if (!in_array($perPage, $allowed)) {
            $perPage = 10;
        }

        $tags = BlogTag::withCount('posts')
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
            
        return view('admin.blog.tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new tag
     */
    public function create()
    {
        return view('admin.blog.tags.create');
    }

    /**
     * Store a newly created tag
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_tags,slug',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        BlogTag::create($validated);

        return redirect()->route('admin.blog.tags.index')
            ->with('success', 'Tag created successfully.');
    }

    /**
     * Show the form for editing the tag
     */
    public function edit(BlogTag $tag)
    {
        return view('admin.blog.tags.edit', compact('tag'));
    }

    /**
     * Update the specified tag
     */
    public function update(Request $request, BlogTag $tag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_tags,slug,' . $tag->id,
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        $tag->update($validated);

        return redirect()->route('admin.blog.tags.index')
            ->with('success', 'Tag updated successfully.');
    }

    /**
     * Remove the specified tag
     */
    public function destroy(BlogTag $tag)
    {
        $tag->posts()->detach();
        $tag->delete();

        return redirect()->route('admin.blog.tags.index')
            ->with('success', 'Tag deleted successfully.');
    }
}
