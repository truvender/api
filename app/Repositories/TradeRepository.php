<?php
namespace App\Repositories;

use App\Models\User;
use App\Services\Signer;
use App\Models\CryptoAsset;
use App\Models\CryptoTrade;
use App\Models\Transaction;
use App\Models\CryptoRecord;
use App\Interfaces\TradeInterface;
use Illuminate\Support\Facades\DB;
use App\Services\BlockcypherService;

class TradeRepository implements TradeInterface {

    public function getTrades()
    {
        
    }


    /**
     * Card Trade
     */
    private function buyCard()
    {
    }

    private function sellCard()
    {
    }

    private function updateCryptoBalance($wallet, BlockcypherService $block)
    {
        $wallet->update([
            'balance' => $block->balance($wallet->address, $wallet->asset->symbol)['final_balance'],
        ]);
        return $wallet->balance;
    }


    /**
     * CryptoTrade
     */
    public function tradeCrypto($request)
    {
        return DB::transaction(function () use ($request) {
            $block = new BlockcypherService();
            $user = User::where('id', auth()->user()->id)->first();
            $asset = CryptoAsset::whereSymbol($request->asset)->first();
            $rates = $asset->rates;
            $value = $request->value;
            $usd_amount = $request->usd_amount;
            $action = $request->action;
            $code = tx_code();

            if ($action == 'sell') {
                $wallet = $user->wallets()->where('type', 'crypto')->where('asset_id', $asset->id)->sole();
                $balance = $this->updateCryptoBalance($wallet, $block);
                $rate = $rates->seller_rate * $usd_amount;
                $check_balance = $balance > $value;

            } else {
                $wallet = $user->wallets()
                    ->where('type', 'fiat')->sole();
                $balance = $wallet->balance;
                $rate = $rates->buyer_rate * $usd_amount;
                $check_balance = $balance > $rate;
            }

            if (!$check_balance) {
                return [
                    'error' => true,
                    'message' =>  'insuficient wallet balance',
                ];
            }

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'trxn_ref' => $code,
                'type' => $action == 'sell' ? 'credit' : 'debit',
                'amount' => $rate,
                'fee' => 0,
            ]);

            $trade = CryptoTrade::create([
                'user_id' => $user->id,
                'asset_id' => $asset->id,
                'sell' => $action == 'sell' ? true : false,
                'amount_usd' => $usd_amount,
                'value' => $value,
                'rate' => $rate,
                'status' => 'pending'
            ]);

            if($action == 'sell'){
                $to = $asset->wallet;
                if($to != null){
                    $createTransaction = $block->createTransaction($wallet->address, $to, $value, $asset->symbol);
                    if($createTransaction['status'] == true){
    
                        $createRecord = CryptoRecord::create([
                            'transaction_id' => $transaction->id,
                            'asset_id' => $asset->id,
                            'tx_hash' => $createTransaction['hash'],
                            'block_height' => $createTransaction['block_height'],
                            'tx_input_n' => $createTransaction['inputs']['addresses'][0],
                            'tx_output_n' => $createTransaction['outputs']['addresses'][0],
                            'value' => $value,
                            'ref_balance' => $wallet->balance,
                        ]);
    
                        $signature = (new Signer())->sign($createTransaction['tosign'], $wallet->private);
    
                        $sendTransaction = $block->sendTransaction(strtolower($asset->symbol), $createTransaction['tosign'], $signature, $wallet->private);
    
                        if ($sendTransaction['status'] == true) {
    
                            $wallet->update([
                                'balance' => $block->balance($wallet->address, $asset->symbol)['final_balance'],
                            ]);
    
                            $defaultWallet = $user->wallets()->where('type', 'fiat')->sole();
                            $defaultWallet->update([
                                'balance' => $defaultWallet->balance + $rate
                            ]);
    
                            return [
                                'error' => false,
                                'message' => 'transaction completed',
                                'data' => $transaction
                            ];
                        }
                    }
                }
                return [
                    'error' => true,
                    'message' => 'could not complete transaction'
                ];

            }else{

                $wallet->update([
                    'balance' => $wallet->balance - $rate
                ]);

                $transaction->update([
                    'status' => 'success'
                ]);

                return [
                    'error' => false,
                    'message' => 'trade successful and awaiting approval'
                ];
            }
        });
    
    }


    /**
     * Other Assets
     */
    public function sellAsset($request)
    {
        
    }

    /**
     * Temp Mobile
     */
    public function buyTempMobile()
    {
        
    }

}