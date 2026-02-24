<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UrlRedirect;
use Illuminate\Http\Request;

class UrlRedirectController extends Controller
{
    public function index()
    {
        $redirects = UrlRedirect::latest()->paginate(50);
        return view('admin.url-redirects.index', compact('redirects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'redirects' => 'required|array',
            'redirects.*.link_from' => 'required|string|max:255',
            'redirects.*.link_to' => 'required|string|max:255',
        ]);

        $savedCount = 0;
        foreach ($request->redirects as $redirect) {
            if (!empty($redirect['link_from']) && !empty($redirect['link_to'])) {
                UrlRedirect::create([
                    'link_from' => $redirect['link_from'],
                    'link_to' => $redirect['link_to'],
                    'is_direct_link' => isset($redirect['is_direct_link']) ? true : false,
                    'is_active' => true
                ]);
                $savedCount++;
            }
        }

        return redirect()->route('admin.url-redirects.index')
            ->with('success', "{$savedCount} redirect(s) saved successfully");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'link_from' => 'required|string|max:255',
            'link_to' => 'required|string|max:255',
        ]);

        $redirect = UrlRedirect::findOrFail($id);
        $redirect->update([
            'link_from' => $request->link_from,
            'link_to' => $request->link_to,
            'is_direct_link' => $request->has('is_direct_link'),
        ]);

        return redirect()->route('admin.url-redirects.index')
            ->with('success', 'Redirect updated successfully');
    }

    public function destroy($id)
    {
        $redirect = UrlRedirect::findOrFail($id);
        $redirect->delete();

        return redirect()->route('admin.url-redirects.index')
            ->with('success', 'Redirect deleted successfully');
    }

    public function toggleStatus($id)
    {
        $redirect = UrlRedirect::findOrFail($id);
        $redirect->update(['is_active' => !$redirect->is_active]);

        $status = $redirect->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.url-redirects.index')
            ->with('success', "Redirect {$status} successfully");
    }
}
