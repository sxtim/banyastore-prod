<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\ActionRequest;
use App\Models\Action;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Http\RedirectResponse;

class PersonalController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        return view('personal.index',compact('user'));
    }
}
