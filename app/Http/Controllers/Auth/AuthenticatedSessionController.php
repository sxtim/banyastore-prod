<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ajax\EmailRequest;
use App\Http\Requests\Ajax\UpdatePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Services\SmsExolveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Password;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Display the login view.
     */
    public function oldLogin(): View
    {
        return view('auth.old');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $credentials = [
            'phone' => $request->input('phone'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json([
                'status' => 'success'
            ]);
        }

        return response()->json([
            'status' => 'error'
        ]);
    }

    public function forgotPassword(EmailRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return response()->json([
            'status' => $status === Password::RESET_LINK_SENT ? 'success' : 'error',
            'd' =>  __($status)
        ]);
    }


    public function resetPassword(string $token): View
    {
        return view('auth.reset-password',compact('token'));
    }

    public function updatePasswordReset(UpdatePasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return response()->json([
            'status' => $status === Password::PASSWORD_RESET ? 'success' : 'error',
            'd' =>  __($status)
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function authBySms(
        Request $request,
        SmsExolveService $smsExolveService
    ): JsonResponse
    {
        $check = $smsExolveService->checkSmsCode($request->input('phone'), $request->input('sms'));
        if ($check === true) {
            $user = User::where('phone','=', $request->input('phone'))->first();
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
