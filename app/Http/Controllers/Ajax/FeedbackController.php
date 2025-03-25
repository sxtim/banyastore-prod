<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ajax\FeedbackRequest;
use App\Mail\MyMailer;
use App\Models\Feedback;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class FeedbackController extends Controller
{
    public function sendData(
        FeedbackRequest $request,
        NotificationService $notificationService
    ): JsonResponse
    {
        Feedback::create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'mess' => $request->input('mess')
        ]);

        $notificationService->newFeedback($request->input('name'), $request->input('phone'), $request->input('mess'));

        return response()->json([
            'status' => 'success',
        ]);
    }
}
