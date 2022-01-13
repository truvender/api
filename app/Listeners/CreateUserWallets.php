<?php

namespace App\Listeners;

use App\Models\Wallet;
use App\Models\CryptoAsset;
use Illuminate\Support\Facades\Http;
use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateUserWallets
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $user = $event->user;
        $cryptos = ['btc', 'ltc', 'eth', 'ngn'];

        foreach ($cryptos as $asset) {
            $wallet = new Wallet();
            $wallet->user_id = $user->id;
            $wallet->type = $asset == 'ngn' ? 'fiat' : 'crypto';
            if ($asset != 'ngn') {
                $getAsset = CryptoAsset::where('symbol', strtoupper($asset))->first();
                if ($getAsset) {

                    $endpoint = blockEndpoint($asset,'addrs');
                    $blockAddress = Http::post($endpoint)->json();

                    $wallet->asset_id = $getAsset->id;
                    $wallet->private = $blockAddress['private'];
                    $wallet->public = $blockAddress['public'];
                    $wallet->address = $blockAddress['address'];
                    $wallet->wif = array_key_exists("wif", $blockAddress) ? $blockAddress['wif'] : null;
                }
            }
            $wallet->balance = 0;
            $wallet->save();
        }

    }
}
