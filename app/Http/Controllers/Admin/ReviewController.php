<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $allowed = [10, 25, 50, 100];
        $perPage = (int) $request->get('perPage', 10);
        if (!in_array($perPage, $allowed)) {
            $perPage = 10;
        }

        $reviews = Review::orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve($id)
    {
        $review = Review::find($id);
        if ($review) {
            $review->status = 1;
            $review->save();
        }
        return redirect()->back()->with('success', 'Review approved.');
    }

    public function disapprove($id)
    {
        $review = Review::find($id);
        if ($review) {
            $review->status = 0;
            $review->save();
        }
        return redirect()->back()->with('success', 'Review disapproved.');
    }

    public function destroy($id)
    {
        $review = Review::find($id);
        if ($review) {
            $review->delete();
        }
        return redirect()->back()->with('success', 'Review deleted.');
    }


}
