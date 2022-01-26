<?php

namespace App\Repositories;

use App\Models\Variation;
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

    /**
     * Buy data with vtu.ng
     */
    private function buyData($data)
    {
        $baseUrl = config('services.vtu.url');
        $user = config('services.vtu.username');
        $pass = config('services.vtu.pass');
        $endpoint = $baseUrl . "data?username=$user&password=$pass&phone=" . $data['customer'] . "&network_id=" . $data['provider'] . "&variation_id=" . $data['variation_id'];
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


    private function createBillTransaction($data, $type)
    {
        $tx_ref = tx_code();
        
        $transaction = Transaction::create([
            'wallet_id' => $data['wallet_id'],
            'trxn_ref' => $tx_ref,
            'type' => 'debit',
            'amount' => $data['amount'],
            'status' => 'success',
            'fee' => 0
        ]);

        $billPurchase = BillPayment::create([
            'transaction_id' => $transaction->id,
            'is_airtime' => true,
            'customer' => $data['customer'],
            'service_provider' => $data['provider'],
            'variation_id' => $type == 'data' ? $data['variation_id'] : null,
            'status' => 'success',
        ]);

        return [
            'transaction' => $transaction,
            'billPurchase' => $billPurchase
        ];
    }


    private function getVariation($variation_code)
    {
        return Variation::where('code', $variation_code)->first();
    }

    public function airtimePurchase($request)
    {

        return DB::transaction(function () use ($request){

            $user = auth()->user();
            $provider = $request->provider;
            $customer = $request->customer;
            $pay_option = $request->pay_from;
            $amount = $request->amount;
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

            $purchase = $this->buyAirtime([
                'customer' => $customer,
                'provider' => $provider,
                'amount' => $amount
            ]);

            if ($purchase['status'] == true) {


                if (strtolower($pay_option) == 'ngn') {
                    $wallet->update([
                        'balance' => $wallet->balance - $amount
                    ]);
                }

                $recordTransaction = $this->createBillTransaction([
                    'provider' => $provider,
                    'amount' => $amount,
                    'wallet_id' => $wallet->id,
                    'customer' => $customer
                ], 'airtime');

                $transaction = $recordTransaction['transaction'];
    
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



    public function prchaseData($request)
    {
        return DB::transaction(function () use ($request) {
            $user = auth()->user();
            $customer = $request->customer;
            $pay_option = $request->pay_from;
            $amount = $request->amount;
            $provider = $request->provider;
            $wallet = $user->wallets()->where('type', 'fiat')->sole();

            $getVariation = $this->getVariation($request->variation_code);
            $variation = $getVariation->code;

            if (strtolower($pay_option) == 'ngn') {
                if ($wallet->balance < $amount) {
                    return [
                        'status' => false,
                        'message' => 'insuficient wallet balance'
                    ];
                }
            } elseif (strtolower($pay_option) != 'ngn' && $request->crypto_value != null) {
                $asset = CryptoAsset::where('symbol', strtoupper($pay_option))->first();
                $wallet = $user->wallets()->where('type', 'crypto')
                    ->whereAssetId($asset->id)->sole();
                $usdRate = $amount / $asset->rates->default;
                // $amount = $request->crypto_value;
            }

            $purchase = $this->buyData([
                'customer' => $customer,
                'provider' => $provider,
                'amount' => $amount,
                'variation_id' => $variation
            ]);

            if ($purchase['status'] == true) {

                if (strtolower($pay_option) == 'ngn') {
                    $wallet->update([
                        'balance' => $wallet->balance - $amount
                    ]);
                }

                $recordTransaction = $this->createBillTransaction([
                    'wallet_id' => $wallet->id,
                    'amount' => $amount,
                    'customer' => $customer,
                    'provider' => $provider,
                    'variation_id' => $getVariation->id
                ], 'data');


                $transaction = $recordTransaction['transaction'];

                return [
                    'status' => true,
                    'message' => $variation . ' data purchase was successful',
                    'data' => $transaction,
                ];
            }
        });
    }
    
}