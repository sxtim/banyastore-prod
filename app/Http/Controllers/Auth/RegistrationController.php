<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\SmsExolveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegistrationController extends Controller
{

    public function index(): View
    {
        return view('auth.register');
    }

    public function registerBySms(
        RegisterRequest $request,
        SmsExolveService $smsExolveService
    ): JsonResponse
    {
        $check = $smsExolveService->checkSmsCode($request->input('phone'), $request->input('sms'));
        if ($check === true) {
            $user = User::create([
                'name' => $request->input('name'),
                'surname' => $request->input('surname'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone')
            ]);
            if ($user) {
                Auth::login($user);
                return response()->json([
                    'status' => 'success'
                ]);
            }
        }
        return response()->json([
            'status' => 'error'
        ]);
    }
}
