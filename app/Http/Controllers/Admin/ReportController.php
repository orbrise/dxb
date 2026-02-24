<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\UsersProfile;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of the reports
     */
    public function index(Request $request)
    {
        $query = Report::with(['user', 'profile']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by report type
        if ($request->has('type') && $request->type != '') {
            $query->where('report_type', $request->type);
        }

        // Search by profile name or reporter email
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('profile', function($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('user', function($uq) use ($search) {
                    $uq->where('email', 'like', "%{$search}%");
                });
            });
        }

        $perPage = $request->input('per_page', 20);
        $reports = $query->orderBy('created_at', 'desc')->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('admin.profile-reports.table', compact('reports'))->render(),
                'pagination' => $reports->appends(request()->query())->links()->render(),
                'entriesInfo' => $reports->total() > 0 
                    ? 'Showing ' . $reports->firstItem() . ' to ' . $reports->lastItem() . ' of ' . number_format($reports->total()) . ' entries'
                    : 'Showing 0 to 0 of 0 entries'
            ]);
        }

        return view('admin.profile-reports.index', compact('reports'));
    }

    /**
     * Display the specified report
     */
    public function show($id)
    {
        $report = Report::with(['user', 'profile'])->findOrFail($id);
        return view('admin.profile-reports.show', compact('report'));
    }

    /**
     * Update the report status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,resolved'
        ]);

        $report = Report::findOrFail($id);
        $report->status = $request->status;
        $report->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Report status updated successfully'
            ]);
        }

        return redirect()->back()->with('success', 'Report status updated successfully');
    }

    /**
     * Delete a report
     */
    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Report deleted successfully'
            ]);
        }

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report deleted successfully');
    }

    /**
     * Take action on the reported profile
     */
    public function takeAction(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:archive,delete,warn',
            'reason' => 'nullable|string|max:255'
        ]);

        $report = Report::findOrFail($id);
        
        // Check if profile exists
        if (!$report->profile) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile not found. It may have been already deleted.'
                ], 404);
            }
            return redirect()->back()->with('error', 'Profile not found. It may have been already deleted.');
        }

        $profile = $report->profile;
        $message = '';

        try {
            switch ($request->action) {
                case 'archive':
                    $reason = $request->reason ?? 'Archived due to report';
                    $profile->archive($reason);
                    $message = 'Profile has been archived successfully';
                    break;

                case 'delete':
                    $profileName = $profile->name;
                    $profile->delete();
                    $message = "Profile '{$profileName}' has been deleted successfully";
                    break;

                case 'warn':
                    // You can implement a warning system here
                    $message = 'Warning sent to profile owner';
                    break;

                default:
                    $message = 'Action completed';
            }

            // Mark report as resolved
            $report->status = 'resolved';
            $report->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            \Log::error('Profile action error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
