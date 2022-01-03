<?php

use Illuminate\Support\Facades\Http;

if (!function_exists('generateToken')) {
    function generateToken()
    {
        $time = substr(time(), 6, 4);
        $code = mt_rand(01, 99);
        return $time . $code;
    }
}

if (!function_exists('cleanPhone')) {
    function cleanPhone($str)
    {
        $prefix = '+';
        if (substr($str, 0, strlen($prefix)) == $prefix) {
            $str = substr($str, strlen($prefix));
        }
        return $str;
    }
}

if (!function_exists('sendSMSToken')) {
    function sendSMSToken($to)
    {
        $data = array(
            "api_key" => config('services.termii.key'),
            "message_type" => "NUMERIC",
            "to" => $to,
            "from" => 'N-Alert',
            "channel" => "dnd",
            "pin_attempts" => 10,
            "pin_time_to_live" =>  5,
            "pin_length" => 6,
            "pin_placeholder" => "< 1234 >",
            "message_text" => "Your verification pin is < 1234 >",
            "pin_attempts" => 10,
            "pin_time_to_live" =>  5,
            "pin_length" => 6,
            "pin_type" => "NUMERIC",
        );

        $endpoint = "https://termii.com/api/sms/otp/send";
        $response = Http::post($endpoint, $data)->json();
        return $response;
    }
}