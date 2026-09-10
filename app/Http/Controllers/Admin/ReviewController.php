<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
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

        // Eager-load the sender (auth user), the reviewed profile, and
        // just enough profile relations to build a clickable
        // /{gender}-escorts-in-{city}/{id}/{slug} link + show a cover
        // thumbnail in the "Review Details" modal.
        $query = Review::with([
            'user:id,name,email',
            'profile:id,user_id,name,slug,gender,city,about',
            'profile.ggender:id,name',
            'profile.getcity:id,name,slug',
            'profile.coverimg',
        ]);

        if ($request->filled('id')) {
            $query->where('id', (int) $request->input('id'));
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->input('user_id'));
        }
        if ($request->filled('profile_id')) {
            $query->where('profile_id', (int) $request->input('profile_id'));
        }
        if ($request->filled('email')) {
            $email = $request->input('email');
            $query->whereHas('user', function ($q) use ($email) {
                $q->where('email', 'like', '%' . $email . '%');
            });
        }
        if ($request->filled('star')) {
            $query->where('star', (int) $request->input('star'));
        }
        // Status: '' = all, '0' = pending, '1' = approved. Guard the
        // literal-'0' case separately since filled() treats "0" as filled
        // but the loose falsy check does not — a subtle footgun.
        if ($request->input('status') !== null && $request->input('status') !== '') {
            $query->where('status', (int) $request->input('status'));
        }
        if ($request->filled('has_reply')) {
            if ($request->input('has_reply') === 'yes') {
                $query->whereNotNull('reply')->where('reply', '!=', '');
            } elseif ($request->input('has_reply') === 'no') {
                $query->where(function ($q) {
                    $q->whereNull('reply')->orWhere('reply', '');
                });
            }
        }
        if ($request->filled('q')) {
            $needle = '%' . $request->input('q') . '%';
            $query->where(function ($q) use ($needle) {
                $q->where('review', 'like', $needle)
                    ->orWhere('reply', 'like', $needle);
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $reviews = $query->orderByDesc('id')
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
            // CacheService::getProfileReviews caches per-profile with a long
            // TTL — without bumping the version here the approved review
            // wouldn't appear on the profile page until the cache expired.
            CacheService::clearProfileCache($review->profile_id);
            \Illuminate\Support\Facades\Cache::forget('cache:recent_reviews:10');
        }
        return redirect()->back()->with('success', 'Review approved.');
    }

    public function disapprove($id)
    {
        $review = Review::find($id);
        if ($review) {
            $review->status = 0;
            $review->save();
            CacheService::clearProfileCache($review->profile_id);
            \Illuminate\Support\Facades\Cache::forget('cache:recent_reviews:10');
        }
        return redirect()->back()->with('success', 'Review disapproved.');
    }

    public function destroy($id)
    {
        $review = Review::find($id);
        if ($review) {
            $profileId = $review->profile_id;
            $review->delete();
            CacheService::clearProfileCache($profileId);
            \Illuminate\Support\Facades\Cache::forget('cache:recent_reviews:10');
        }
        return redirect()->back()->with('success', 'Review deleted.');
    }


}
