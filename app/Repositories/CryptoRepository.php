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
    
}