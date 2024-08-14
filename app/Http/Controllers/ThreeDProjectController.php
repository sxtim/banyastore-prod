<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ThreeDProjectController extends Controller
{
    public function index(): View
    {
        return view('three-d-project');
    }
}
