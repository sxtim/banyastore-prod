<?php

namespace App\Http\Controllers;

use App\Models\Action;
use Illuminate\Contracts\View\View;

class ActionController extends Controller
{
    public function index(): View
    {
        $actions = Action::where(function($query) {
            $query->where('start_at', '<=', now())
                ->orWhere('start_at', '=', null);
        })
            ->where(function($query) {
                $query->where('end_at','>=', now())
                    ->orWhere('end_at', '=', null);
            })
            ->where('is_active', true)
            ->orderBy('sort', 'desc')
            ->get();
        return view('action.index', compact('actions'));
    }

    public function detail(string $slug): View
    {
        $action = Action::where(function($query) {
            $query->where('start_at', '<=', now())
                ->orWhere('start_at', '=', null);
        })
            ->where(function($query) {
                $query->where('end_at','>=', now())
                    ->orWhere('end_at', '=', null);
            })
            ->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();

        return view('action.detail', compact('action'));
    }
}
