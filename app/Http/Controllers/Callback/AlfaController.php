<?php

namespace App\Http\Controllers\Callback;

use App\Http\Controllers\Controller;
use App\Models\Payment\PaymentLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AlfaController extends Controller
{
    public function callback(
        Request         $request
    ): Response
    {
        PaymentLog::create([
            'method' => 'callback',
            'response' => $request->all()
        ]);

        return response('', 200);
    }
}
