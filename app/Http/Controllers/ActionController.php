<?php

namespace App\Http\Controllers;

use App\Models\Action;
use Illuminate\Contracts\View\View;

class ActionController extends Controller
{
    public function index(): View
    {
        $actions = Action::where('start_at', '<=', now())
            ->where('end_at','>=', now())
            ->where('is_active', true)
            ->get();
        return view('action.index', compact('actions'));
    }

    public function detail(): View
    {
        $action = Action::where('start_at', '<=', now())
            ->where('end_at','>=', now())
            ->where('is_active', true)
            ->firstOrFail();

        return view('action.detail', compact('action'));
    }
}
