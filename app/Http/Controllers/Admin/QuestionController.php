<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;

class QuestionController extends Controller
{
    
    public function index(Request $request)
    {
        $allowed = [10, 25, 50, 100];
        $perPage = (int) $request->get('perPage', 10);
        if (!in_array($perPage, $allowed)) {
            $perPage = 10;
        }

        $query = Question::with('askedBy:id,email');

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
            $query->whereHas('askedBy', function ($q) use ($email) {
                $q->where('email', 'like', '%' . $email . '%');
            });
        }
        // Status: '' = all, '0' = pending, '1' = approved. See ReviewController
        // for why filled() alone isn't enough for the literal-'0' case.
        if ($request->input('status') !== null && $request->input('status') !== '') {
            $query->where('status', (int) $request->input('status'));
        }
        if ($request->filled('has_answer')) {
            if ($request->input('has_answer') === 'yes') {
                $query->whereNotNull('answer')->where('answer', '!=', '');
            } elseif ($request->input('has_answer') === 'no') {
                $query->where(function ($q) {
                    $q->whereNull('answer')->orWhere('answer', '');
                });
            }
        }
        if ($request->filled('q')) {
            $needle = '%' . $request->input('q') . '%';
            $query->where(function ($q) use ($needle) {
                $q->where('question', 'like', $needle)
                    ->orWhere('answer', 'like', $needle);
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $questions = $query->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.questions.index', compact('questions'));
    }

    public function approve($id)
    {
        $question = Question::find($id);
        if ($question) {
            $question->status = '1';
            $question->save();
        }
        return redirect()->back()->with('success', 'Question approved.');
    }

    public function disapprove($id)
    {
        $question = Question::find($id);
        if ($question) {
            $question->status = '0';
            $question->save();
        }
        return redirect()->back()->with('success', 'Question disapproved.');
    }

    public function destroy($id)
    {
        $question = Question::find($id);
        if ($question) {
            $question->delete();
        }
        return redirect()->back()->with('success', 'Question deleted.');
    }
}
