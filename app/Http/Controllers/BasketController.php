<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class BasketController extends Controller
{
    public function index(): View
    {
        return view('basket.index');
    }
}
