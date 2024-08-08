<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class FeedBackController extends Controller
{
    public function index(): View
    {
        return view('feed-back-form');
    }
}
