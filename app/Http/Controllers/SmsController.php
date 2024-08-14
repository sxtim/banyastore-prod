<?php

namespace App\Http\Controllers;

use App\Services\SmsExolveService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{
    public function sendPhone(
        SmsExolveService $smsExolveService,
        Request $request
    ): JsonResponse
    {
        try {
            $smsExolveService->sendSms($request->input('phone'));
            return response()->json([
                'status' => 'success'
            ]);
        } catch (\Exception $e) {dd($e->getMessage());
            Log::error($e->getMessage());
            return response()->json([
                'status' => 'error'
            ]);
        }
    }
}
