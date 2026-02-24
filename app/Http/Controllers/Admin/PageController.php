<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    // Show all pages
    public function index(Request $request)
    {
        $allowed = [10, 25, 50, 100];
        $perPage = (int) $request->get('perPage', 10);
        if (!in_array($perPage, $allowed)) {
            $perPage = 10;
        }

        $pages = Page::ordered()->paginate($perPage)->withQueryString();
        return view('admin.pages.index', compact('pages'));
    }

    // Show create page form
    public function create()
    {
        return view('admin.pages.create');
    }

    // Store new page
    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        
        $slug = \Str::slug($request->title);

        // Get the next order index
        $maxOrder = Page::max('order_index') ?? 0;

        Page::create([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content ?? '',
            'is_published' => $request->has('is_published'),
            'order_index' => $maxOrder + 1,
        ]);

        return redirect()->route('pages.index')->with('success', 'Page created successfully.');
    }

    // Show edit page form
    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    // Update page
    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
    
        $slug = \Str::slug($request->title);
        
        // Update with explicit boolean casting for is_published
        $page->update([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'is_published' => (bool)$request->is_published
        ]);
    
        return redirect()->route('pages.index')->with('success', 'Page updated successfully.');
    }

    // Delete page
    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('pages.index')->with('success', 'Page deleted successfully.');
    }

    // Update page order via AJAX
    public function updateOrder(Request $request)
    {
        $request->validate([
            'pages' => 'required|array',
            'pages.*' => 'required|integer|exists:pages,id'
        ]);

        foreach ($request->pages as $index => $pageId) {
            Page::where('id', $pageId)->update(['order_index' => $index + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Page order updated successfully']);
    }
}
