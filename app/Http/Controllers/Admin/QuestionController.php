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

        $questions = Question::orderByDesc('id')
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
