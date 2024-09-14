<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Contracts\View\View;

class FeedbackController extends Controller
{
    public function index(): View
    {
        $feedbacks = Feedback::orderBy('id', 'desc')->paginate(15);
        return view('backend.feedback.index', compact('feedbacks'));
    }

    public function show(int $id): View
    {
        $feedback = Feedback::findOrFail($id);
        return view('backend.feedback.show', compact('feedback'));
    }
}
