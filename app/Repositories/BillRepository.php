<?php

namespace App\Repositories;

use App\Models\BillPayment;
use App\Models\CryptoAsset;
use App\Models\Transaction;
use App\Interfaces\BillInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class BillRepository implements BillInterface {


    private function buyAirtime($data)
    {
        $baseUrl = config('services.vtu.url');
        $user = config('services.vtu.username');
        $pass = config('services.vtu.pass');
        $endpoint = $baseUrl . "airtime?username=$user&password=$pass&phone=" . $data['customer'] . "&network_id=" . $data['provider'] . "&amount=" . $data['amount'];
        $request = Http::get($endpoint)->json();
        if ($request['code'] == 'success') {
            return [
                'status' => true,
                'data' => $request['data'],
            ];
        }

        return [
            'status' => false,
            'data' => null,
            'message' => $request['message'],
        ];
    }

    public function airtimePurchase($request)
    {

        return DB::transaction(function () use ($request){

            $user = auth()->user();
            $provider = $request->provider;
            $customer = $request->customer;
            $pay_option = $request->pay_from;
            $amount = $request->amount;
            $tx_ref = tx_code();
            $wallet = $user->wallets()->where('type', 'fiat')->sole();
            
            if(strtolower($pay_option) == 'ngn'){
                $debitAmount = $amount;
                if ($wallet->balance < $amount) {
                    return [
                        'status' => false,
                        'message' => 'insuficient wallet balance'
                    ];
                }
            }elseif(strtolower($pay_option) != 'ngn' && $request->crypto_value != null )
            {
                $asset = CryptoAsset::where('symbol', strtoupper($pay_option))->first();
                $wallet = $user->wallets()->where('type', 'crypto')
                    ->whereAssetId($asset->id)->sole();
                $usdRate = $amount / $asset->rates->default;
                // $amount = $request->crypto_value;
            }

            $fee = 0;

            $purchase = $this->buyAirtime([
                'customer' => $customer,
                'provider' => $provider,
                'amount' => $amount
            ]);

            if ($purchase['status'] == true) {
        
                $transaction = Transaction::create([
                    'wallet_id' => $wallet->id,
                    'trxn_ref' => $tx_ref,
                    'type' => 'debit',
                    'amount' => $amount,
                    'status' => $purchase['status'] == true ? 'success' : 'failed',
                    'fee' => $fee
                ]);
    
                $billPurchase = BillPayment::create([
                    'transaction_id' => $transaction->id,
                    'is_airtime' => true,
                    'customer' => $customer,
                    'service_provider' => $provider,
                    'status' => $purchase['status'] == true ? 'success' : 'failed',
                ]);
    
                if (strtolower($pay_option) == 'ngn') {
                    $wallet->update([
                        'balance' => $wallet->balance - $amount
                    ]);
                }
    
                // event(new TransactionSuccess($transaction));
    
                return [
                    'status' => true,
                    'message' => 'airtime purchase was successful',
                    'data' => $transaction->withAssociate,
                ];
            }
    
            return [
                'status' => false,
                'message' => 'could not complete request',
                'data' => null,
            ];
            
        });
    }
    
}