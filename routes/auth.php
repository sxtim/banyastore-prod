<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
//    Route::get('register', [RegisteredUserController::class, 'create'])
//                ->name('register');
//
//    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('register', [\App\Http\Controllers\Auth\RegistrationController::class, 'index'])
        ->name('register');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::get('old-login', [AuthenticatedSessionController::class, 'oldLogin'])
        ->name('old-login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::post('ajax/send-sms', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'authBySms'])
        ->name('ajax.send-sms');

    Route::post('ajax/register', [\App\Http\Controllers\Auth\RegistrationController::class, 'register'])
        ->name('ajax.register');

//    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
//                ->name('password.request');
//
//    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
//                ->name('password.email');
//
//    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
//                ->name('password.reset');
//
//    Route::post('reset-password', [NewPasswordController::class, 'store'])
//                ->name('password.store');
});

Route::middleware('auth')->group(function () {
//    Route::get('verify-email', EmailVerificationPromptController::class)
//                ->name('verification.notice');
//
//    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
//                ->middleware(['signed', 'throttle:6,1'])
//                ->name('verification.verify');
//
//    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
//                ->middleware('throttle:6,1')
//                ->name('verification.send');
//
//    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
//                ->name('password.confirm');
//
//    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
//
//    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});
