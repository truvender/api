<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\CryptoAsset;
use App\Models\CryptoTrade;
use Illuminate\Support\Facades\DB;
use App\Interfaces\CryptoInterface;

class CryptoRepository implements CryptoInterface {


    public function listAssets()
    {
        return CryptoAsset::orderBy('name', 'asc')->with('rates')->get()->map(function($asset){
            $rate = $asset->rates;
            return [
                'id' => $asset->id,
                'name' => $asset->name,
                'symbol' => $asset->symbol,
                'is_available' => $asset->is_available,
                'symbol' => $asset->symbol,
                'buyer_rate' => $rate ? $rate->buyer_rate : 0,
                'seller_rate' => $rate ? $rate->seller_rate : 0,
            ];
        });
    }


    private function updateBalance($wallet, $currency)
    {
        $newBalance = addressBalance($currency, $wallet->address);
        $wallet->update([
            'balance' => $newBalance
        ]);
        return $newBalance;
    }


    public function trade($request)
    {
        return DB::transaction(function () use ($request) {

            $user = User::where('id', auth()->user()->id)->first();
            $asset = CryptoAsset::whereSymbol($request->asset)->first();
            $rates = $asset->rates;
            $value = $request->value;
            $usd_amount = $request->usd_amount;
            $action = $request->action;
            $code = tx_code();
            
            $rate = $action == 'sell' ? $rates->seller_rate * $usd_amount 
                : $rates->buyer_rate * $usd_amount;
            
            if($action == 'sell'){
                $wallet = $user->wallets()
                    ->where('type', 'crypto')
                    ->where('asset_id', $asset->id)->sole();
                $balance = $this->updateBalance($asset->currency, $wallet);
                $rate = $rates->seller_rate * $usd_amount;
                $check_balance = $balance > $value;
            }else{
                $wallet = $user->wallets()
                    ->where('type', 'fiat')->sole();
                $balance = $wallet->balance;
                $rate = $rates->buyer_rate * $usd_amount;
                $check_balance = $balance > $rate;
            }

            if(!$check_balance){
                return [
                    'error' => true,
                    'message' =>  'insuficient wallet balance',
                ];
            }
            
            $trade = CryptoTrade::create([
                'user_id' => $user->id,
                'asset_id' => $asset->id,
                'sell' => $action == 'sell' ? true : false,
                'amount_usd' => $usd_amount,
                'value' => $value,
                'rate' => $rate,
                'status' => 'pending'
            ]);
            
        });
    }


    
}