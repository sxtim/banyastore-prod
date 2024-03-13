<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;

class UserController extends Controller
{

    public function index(): View
    {
        $users = User::paginate(15);
        return view('backend.users.index', compact('users'));
    }
}
