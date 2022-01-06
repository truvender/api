<?php

namespace Database\Seeders;

use App\Models\CryptoAsset;
use Illuminate\Database\Seeder;

class Cryptos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CryptoAsset::truncate();
        $cryptos = [
            [
                'name' => 'Bitcoin',
                'symbol' => 'BTC',
                'is_available' => true,
                'image' => 'https://cryptologos.cc/logos/bitcoin-btc-logo.svg'
            ],
            [
                'name' => 'Ethereum',
                'symbol' => 'ETH',
                'is_available' => true,
                'image' => 'https://cryptologos.cc/logos/ethereum-eth-logo.svg'
            ],
            [
                'name' => 'Litecoin',
                'symbol' => 'LTC',
                'is_available' => true,
                'image' => 'https://cryptologos.cc/logos/litecoin-ltc-logo.svg'
            ],
        ];


        foreach ($cryptos as $crypto) {
            CryptoAsset::create([
                'name' => $crypto['name'],
                'symbol' => $crypto['symbol'],
                'image' => $crypto['image'],
                'is_available' => $crypto['is_available']
            ]);
        }
    }
}
