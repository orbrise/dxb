<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VerificationPhoto;
use App\Mail\VerificationRejected;
use Illuminate\Support\Facades\Mail;
 
class VerificationController extends Controller
{
    public function index(Request $request) 
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        
        $query = VerificationPhoto::with(['user', 'profile.ggender', 'profile.getcity'])
            ->where('status', 'pending');
            
        // Apply search filter
        if ($search) {
            $query->whereHas('profile', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }
        
        $photos = $query->latest()->paginate($perPage);
            
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
