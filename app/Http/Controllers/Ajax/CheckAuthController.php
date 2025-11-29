<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class CheckAuthController extends Controller
{
    public function check(): JsonResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            return response()->json([
                'status' => true,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
            ]);
        }

        return response()->json([
            'status' => false,
        ]);
    }
}
