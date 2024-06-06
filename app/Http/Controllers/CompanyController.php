<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class CompanyController extends Controller
{
    public function index(): View
    {
        return view('about-company');
    }
}
