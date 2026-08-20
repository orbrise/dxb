<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VerificationPhoto;
use App\Mail\VerificationApproved;
use App\Mail\VerificationRejected;
use Illuminate\Support\Facades\Mail;
 
class VerificationController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        $profileId = $request->get('profile_id');
        $userId = $request->get('user_id');
        $email = $request->get('email');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = VerificationPhoto::with(['user', 'profile.ggender', 'profile.getcity'])
            ->where('status', 'pending');

        if ($search) {
            $query->whereHas('profile', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }

        if ($profileId !== null && $profileId !== '') {
            $query->where('profile_id', $profileId);
        }

        if ($userId !== null && $userId !== '') {
            $query->where('user_id', $userId);
        }

        if ($email) {
            $query->whereHas('user', function ($q) use ($email) {
                $q->where('email', 'LIKE', "%{$email}%");
            });
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $photos = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.verifications.index', compact('photos'));
    }

    public function approve($id)
    {
        $photo = VerificationPhoto::findOrFail($id);
        $photo->update([
            'status' => 'approved',
            'verified_at' => now(),
            'verified_by' => auth()->id()
        ]);

        $photo->profile->update(['is_verified' => true]);

        // Notify the owner that their listing has been verified. Same
        // try/catch pattern as reject() so a mail hiccup never blocks the
        // JSON response the admin panel is waiting on.
        if ($photo->profile && $photo->user && $photo->user->email) {
            try {
                Mail::to($photo->user->email)->send(new VerificationApproved([
                    'profileName' => $photo->profile->name,
                    'userName' => $photo->user->name ?? $photo->profile->name,
                    'profileUrl' => url('my-profile/'.$photo->profile->slug.'/'.$photo->profile->id),
                ]));
            } catch (\Exception $e) {
                \Log::error('Failed to send verification approval email: ' . $e->getMessage());
            }
        }

        return response()->json(['success' => true]);
    }
    
    public function reject(Request $request, $id)
    {
        $photo = VerificationPhoto::findOrFail($id);
        $photo->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
            'rejection_link' => $request->link,
            'verified_at' => now(),
            'verified_by' => auth()->id()
        ]);
        
        // Send rejection email to user
        if ($photo->profile && $photo->user && $photo->user->email) {
            try {
                Mail::to($photo->user->email)->send(new VerificationRejected([
                    'profileName' => $photo->profile->name,
                    'userName' => $photo->user->name ?? $photo->profile->name,
                    'reason' => $request->reason,
                    'actionLink' => $request->link
                ]));
            } catch (\Exception $e) {
                // Log the error but don't fail the rejection
                \Log::error('Failed to send verification rejection email: ' . $e->getMessage());
            }
        }
        
        return response()->json(['success' => true]);
    }
}
