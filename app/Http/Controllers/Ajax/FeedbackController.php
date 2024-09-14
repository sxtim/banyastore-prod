<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ajax\FeedbackRequest;
use App\Mail\MyMailer;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;

class FeedbackController extends Controller
{
    public function sendData(
        FeedbackRequest $request,
        MyMailer $myMailer
    ): JsonResponse
    {
        Feedback::create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'mess' => $request->input('mess')
        ]);


        $mailText = 'На сайте banyastore.ru новое сообщение от пользователя '.$request->input('name') . ' с телефоном '.$request->input('phone');
        $myMailer->sendEmail('opt@feringermsk.ru','Обратная связь',$mailText);

        return response()->json([
            'status' => 'success',
        ]);
    }
}
