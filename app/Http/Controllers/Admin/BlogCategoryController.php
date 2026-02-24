<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of blog categories
     */
    public function index(Request $request)
    {
        $allowed = [10, 25, 50, 100];
        $perPage = (int) $request->get('perPage', 10);
        if (!in_array($perPage, $allowed)) {
            $perPage = 10;
        }

        $categories = BlogCategory::withCount('posts')
            ->ordered()
            ->paginate($perPage)
            ->withQueryString();
            
        return view('admin.blog.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('admin.blog.categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_categories,slug',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        BlogCategory::create($validated);

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the category
     */
    public function edit(BlogCategory $category)
    {
        return view('admin.blog.categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, BlogCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category
     */
    public function destroy(BlogCategory $category)
    {
        // Check if category has posts
        if ($category->posts()->count() > 0) {
            return redirect()->route('admin.blog.categories.index')
                ->with('error', 'Cannot delete category with existing posts. Please reassign posts first.');
        }

        $category->delete();

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
