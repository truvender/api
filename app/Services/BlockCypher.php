<?php

namespace App\Services;

use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Buffertools\Buffer;
use App\Interfaces\CryptoService;
use Illuminate\Support\Facades\Http;
use BitWasp\Bitcoin\Crypto\Random\Rfc6979;
use BitWasp\Bitcoin\Key\Factory\PrivateKeyFactory;
use BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;

class BlockcypherService
{

    protected $key;
    protected $ev;
    protected $base_url;

    public function __construct()
    {
        $this->key = config('services.block_cypher.token');
        $this->ev = config('services.block_cypher.env');
        $this->base_url = config('services.block_cypher.url');
    }


    public function balance($wallet, $currency_symbol)
    {
        $endpoint = blockEndpoint($currency_symbol, "addrs/$wallet", false);
        $request = Http::get($endpoint)->json();
        return $request;
    }

    public function createAddress($currency_symbol)
    {
        $currency_symbol = strtolower($currency_symbol);
        $endpoint = blockEndpoint($currency_symbol, "addrs");
        $blockAddress = Http::post($endpoint)->json();
        return $blockAddress;
    }

    public function createWallet($name, $currency_symbol, $addresses)
    {
        $endpoint = blockEndpoint($currency_symbol, "wallets");
        $blockWallet = Http::post($endpoint, [
            'name' => $name,
            'addresses' => $addresses
        ])->json();

        return $blockWallet;
    }


    public function addAddressToWallet($currency_symbol, $addresses, $name)
    {
        $endpoint = blockEndpoint($currency_symbol, "wallets/$name/addresses");
        $addAddress = Http::post($endpoint, [
            'addresses' => $addresses
        ])->json();

        return $addAddress;
    }



    public function createTransaction($from, $to, $value, $currency_symbol)
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

        $endpoint = blockEndpoint($currency_symbol, "txs/new/");
        $createTx = Http::post($endpoint, $data)->json();
        return $createTx;
    }

    public function sendTransaction($currency_symbol, $tosign, $signatures, $keys)
    {
        $data = [
            "tosign" => [
                $tosign
            ],
            "signatures" => [
               $signatures
            ],
            "pubkeys" => [
               $keys
            ]
        ];

        $endpoint = blockEndpoint($currency_symbol, "txs/send");
        $sendTx = Http::post($endpoint, $data)->json();
        return $sendTx;   
    }


    public function trxDetails($transaction_hash, $currency_symbol)
    {
        $endpoint = $this->base_url . "$currency_symbol/$this->ev/txs/" . $transaction_hash;
        $tx = Http::get($endpoint)->json();
        return $tx;
    }
}
