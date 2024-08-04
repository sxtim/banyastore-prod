<?php

namespace App\Services;


use App\Models\SmsCode;
use Illuminate\Support\Facades\Http;

class SmsExolveService
{
    public function __construct(
        private readonly string $url,
        private readonly string $apiKey
    )
    {
    }

    public function sendSms(string $phone): void
    {
        $code = mt_rand(1001, 9999);
        $data = [
            'number' => '79346617130',
            'destination' => $phone,
            'text' => 'Ваш код: '.$code
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->apiKey
        ])->post($this->url.'/messaging/v1/SendSMS', $data)->json();

        if (!isset($response['message_id'])) {
            throw new \Exception('Error send sms');
        }

        SmsCode::create([
            'phone' => $phone,
            'code' => $code,
            'created_at' => now()
        ]);
    }

    public function checkSmsCode(string $phone, string $code): bool
    {
        $smsCode = SmsCode::where('code','=',$code)->where('phone','=',$phone)->orderBy('desc','id')->first();

        return $smsCode !== null && $smsCode->id;
    }
}
