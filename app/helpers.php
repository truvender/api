<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

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

if (!function_exists('verifySMSToken')) {
    function verifySMSToken($pin_id, $pin)
    {
        $data = array(
            "api_key" => config('services.termii.key'),
            "pin_id" => $pin_id,
            "pin" => $pin,
        );

        $endpoint = "https://termii.com/api/sms/otp/verify";
        $response = Http::post($endpoint, $data)->json();

        return $response;
    }
}


if (!function_exists('generatePasswordResetCode')) {
    function generatePasswordResetCode()
    {
        $partOne = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 3);
        $partTwo = substr(str_shuffle("0123456789"), 0, 4);
        $code = $partOne . $partTwo;
        return str_shuffle($code);
    }
}


if (!function_exists('uploadFile')) {
    function uploadFile($file, $path)
    {
        $path = Storage::disk(env('FILESYSTEM_DRIVER'))->put($path, $file);
        return $path; 
    }
}

if (!function_exists('getFileUrl')) {
    function getFileUrl($path)
    {
        return Storage::disk(env('FILESYSTEM_DRIVER'))->url($path);
    }
}


if (!function_exists('deleteFile')) {
    function deleteFile($path)
    {
        return Storage::disk(env('FILESYSTEM_DRIVER'))->delete($path);
    }
}

if (!function_exists('blockEndpoint')) {
    function blockEndpoint($currency_symbol, $endpoint, $useToken = true)
    {
        $key = config('services.block_cypher.token');
        $ev = config('services.block_cypher.env');
        $base_url = config('services.block_cypher.url');

        return $useToken ? $base_url . "/$currency_symbol/$ev/$endpoint?token=" . $key 
            : $base_url . "$currency_symbol/$ev/$endpoint";
    }
}


if (!function_exists('createCryptoTransaction')) {
    function createCryptoTransaction($from, $to, $value, $currency_symbol)
    {
        $data = [
            "inputs" => [
                ["addresses" => [$to]]
            ],
            "outputs" => [
                [
                    "addresses" => [$from],
                    "value" => $value
                ]
            ]
        ];

        $endpoint = blockEndpoint($currency_symbol, 'txs/new');
        $createTx = Http::post($endpoint, $data)->json();

        return $createTx;
    }
}

if (!function_exists('cryptoTxDetails')) {
    function cryptTxDetails($transaction_hash, $currency_symbol)
    {
        $endpoint = blockEndpoint($currency_symbol, 'txs/'. $transaction_hash, false);
        $tx = Http::get($endpoint)->json();
        return $tx;
    }
}

if (!function_exists('tx_code')) {
    function tx_code()
    {
        $codeWithEntropy = uniqid('TRTX-', true);
        $split = explode('.', $codeWithEntropy);
        $code = $split[0] . '-' . $split[1];
        return $code;
    }
}


/**
 * validate fiat Transactions
 */
if (!function_exists("validateTx")) {
    function validateTx($ref)
    {
        $base_url = config('services.flutterwave.root-url');
        $key = config('services.flutterwave.secrete_key');

        $endpoint = $base_url . "transactions/$ref/verify";
        $request = Http::withHeaders(['Authorization' => 'Bearer ' . $key])->get($endpoint)
            ->json();

        $data = $request['data'];
        if($request['status'] == 'success'){
            return [
                'status' => true,
                'verified' => $data['status'] == 'successful' ? true : false,
                'data' => $data,
            ];
        }
        return [ 'status' => false, 'verified' => false, 'data' => null ];

    }
}

if (!function_exists('getTxFee')) {
    function getTxFee($amount)
    {
        $percentage = config('services.tx_fees.');
        $fee = ($amount / 100) * $percentage;
        return $fee;
    }
}

if (!function_exists('validateAccount')) {
    function validateAccount($bank, $account)
    {
        $base_url = config('services.flutterwave.root-url');
        $endpoint = $base_url . 'accounts/resolve';
        $key = config('services.flutterwave.secrete_key');

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $key])->post($endpoint, [
            'account_number' => $account,
            'account_bank' => $bank
        ])->json();

        if ($response['status'] == 'success') {
            return [
                'status' => true,
                'data' => $response['data']
            ];
        }
        return [
            'status' => false,
            'data' => null
        ];
    }
}

if (!function_exists('bankTransfer')) {
    function bankTransfer(array $data) : array
    {
        $base_url = config('services.flutterwave.root-url');
        $key = config('services.flutterwave.secrete_key');
        //Transfer Endpoint
        $endpoint = $base_url.'transfers';
        
        $request = Http::withHeaders(['Authorization' => 'Bearer ' . $key])->post($endpoint, [
            'account_bank' => $data['bank_code'],
            'account_number' => $data['account_number'],
            'amount' => $data['amount'],
            'naration' => $data['ref'],
            'currency' => 'NGN',
            'beneficiary_name' => $data['account_name'],
            'reference' => strtolower($data['ref']),
            'callback_url' => $data['callback'],
        ])->json();

        if($request['status'] == 'success'){
            return [
                'status' => true,
                'data' => $request['data'],
            ];
        }else{
            return [
                'status' => false,
                'data' => null
            ];
        }
    }
}


if (!function_exists('cryptoMarketPrice')){
    function cryptoMarketPrice($id)
    {
        $endpoint = "https://api.coingecko.com/api/v3/coins/$id?localization=false&tickers=false&community_data=true&developer_data=true&sparkline=false";
        $request  = Http::get($endpoint)->json();
    }
}

