<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    /**
     * Display the login view.
     */
    public function index(): View
    {
        return view('auth.register');
    }
}
