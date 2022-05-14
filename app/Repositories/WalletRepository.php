<?php

namespace App\Repositories;

use App\Models\Bank;
use App\Models\User;
use App\Models\Config;
use App\Models\Wallet;
use App\Models\Country;
use App\Models\Deposit;
use App\Models\FiatRate;
use App\Models\Transfer;
use App\Models\CryptoAsset;
use App\Models\Transaction;
use App\Models\BankTransfer;
use App\Models\TransactionMeta;
use Illuminate\Support\Facades\DB;
use App\Interfaces\WalletInterface;
use App\Models\CryptoRecord;
use App\Services\BlockcypherService;
use App\Services\Signer;

class WalletRepository implements WalletInterface {

    public function getWallet($wallet_id)
    {
        return Wallet::whereId($wallet_id)->with('transactions')->first();
    }

    public function getTransaction($transaction_id)
    {
        return Transaction::whereId($transaction_id)->with('metas')->first();
    }

    public function getWallets()
    {
        $user = User::whereId(auth()->user()->id)->first();
        $wallets = $user->wallets->map(function($wallet){
            $transactions = $wallet->transactions;
            
            $item = [
                "id" => $wallet->id,
                "type" => $wallet->type,
                "balance" => $wallet->balance,
                "address" => $wallet->address,
                "transactions" => $transactions,
            ];

            if ($wallet->type != 'fiat') {
                $item["asset"] = [
                    "name" => $wallet->asset->name,
                    "symbol" => $wallet->asset->symbol,
                    "image" => $wallet->asset->image,
                ];
            }
            return $item;

        });
        return $wallets;
    }

    
    public function fundDefaultWallet($request)
    {
        return DB::transaction(function () use ($request) {
            $user = User::whereId(auth()->user()->id)->first();
            $code = tx_code();
            $wallet = $user->wallets()->where('type', 'fiat')->sole();
            $profile = $user->profile;
    
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'trxn_ref' => $code,
                'type' => 'credit',
                'amount' => $request->amount,
                'fee' => 0,
            ]);

            $transactionMeta = TransactionMeta::create([
                'key' => 'reason',
                'value' => 'deposit'
            ]);
    
            $funding = Deposit::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'transaction_id' => $transaction->id
            ]);
    
    
            return [
                'trxn_ref' => $transaction->trxn_ref,
                'amount' => $transaction->amount,
                'customer' => [
                    'email' => $user->email,
                    'phone' => cleanPhone($user->phone),
                    'name' => $profile->firstname . ' ' . $profile->lastname,
                ],
            ];
        });
    }


    public function completeDefaultWalletFunding($request)
    {
        return DB::transaction(function () use ($request) {
            
            $user = User::whereId(auth()->user()->id)->first();

            $wallet = $user->wallets()->where('type', 'fiat')->sole();

            $validation = validateTx($request->reference);
    
            if ($validation['status'] == true && $validation['verified'] == true) {

                $transaction = $wallet->transactions()->where('trxn_ref', $request->trxn_ref)->sole();

                if ($transaction) {

                    $transaction->update(['status' => 'success']);

                    $oldBalance = $wallet->balance;
                    $newBalance = $oldBalance + $request->amount;

                    $wallet->update([ 'balance' => $newBalance ]);
    
                    // event(new TransactionSuccess($transaction));
    
                    return [
                        'error' => false,
                        'message' => 'wallet funded successfully!'
                    ];
                }
    
                return [
                    'error' => true,
                    'message' => 'unrecognize transaction!'
                ];
            }
            return [
                'error' => true,
                'message' => 'could not process trnasaction'
            ];
        }); 
    }


    private function transferToUser(User $user, int $amount): bool
    {
        $wallet = $user->ngnWallet;
        $oldBalance = $wallet->balance;
        $newBalance = $oldBalance + $amount;
        $wallet->update([
            'balance' => $newBalance
        ]);
        return true;
    }

    private function bankTransfer($account, $amount, $code)
    {
        $data = [
            'bank_code' => $account->bank->code,
            'account_number' => $account->acc_number,
            'amount' => $amount,
            // 'naration' => $request->note,
            'account_name' => $account->acc_name,
            'ref' => $code,
        ];

        $bankTransfer = bankTransfer($data);

        return $bankTransfer['status'] ? [
            'status' => true,
            'data' => $bankTransfer['data']
        ] : [
            'status' => false
        ];
    }



    /**
     * Performs Fiat Transfer
     */
    public function transfer($request)
    {
        $user = User::whereId(auth()->user()->id)->first();
        $wallet = $user->wallets()->where('type', 'fiat')->sole();
        $code = tx_code();
        $amount = $request->amount;
        $fee = getTxFee($amount);
        $currency = $request->currency;
        $type = $request->type;

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'trxn_ref' => $code,
            'type' => 'debit',
            'amount' => $amount,
            'fee' => $fee,
        ]);

        $transactionMeta = TransactionMeta::insert([
            ['key' => 'reason',
            'value' => 'transfer', 'transaction_id' => $transaction->id],
            ['key' => 'transfer_type',
            'value' => $type, 'transaction_id' => $transaction->id]
        ]);


        if ($type == 'account') {
            $account = User::where('email', $request->email)->sole();
            
            if (!$account) {
                return [
                    'error' => true,
                    'message' => 'User does not exist'
                ];
            }

            $oldBalance = $wallet->balance;
            $newBalance = $oldBalance - ($amount + $fee);

            $accountTransfer = $this->transferToUser($account, $request->amount);
            
            if($accountTransfer){
                $wallet->update([ 'balance' => $newBalance ]);
                $transfer =  Transfer::create([
                    'wallet_id' => $wallet->id,
                    'transaction_id' => $transaction->id,
                    'reciever_id' => $account->id,
                ]);
                $transaction->update(['status' =>'success']);
            }
            
        }else {

            $account = $user->bankAccount()->where('acc_number', $request->account_number)->first();
            $bank  = !$request->b2b ? $account->bank : Bank::whereCode($request->account_bank)->first();
            $getCurrency = Country::where('currency_code', $currency)->first();

            $bankTransfer = BankTransfer::create([
                'transaction_id' => $transaction->id,
                'wallet_id' => $wallet->id,
                'bank_id' => $bank,
                'account_number' => !$request->b2b ? $user->bankAccount->account_number : $request->account_number,
                'account_name' => !$request->b2b ? $user->bankAccount->account_name : $request->account_name,
                'b2b' => $request->b2b,
                'country_id' => $getCurrency->id
            ]);

            if ($currency == 'NGN' && $request->b2b == false) {
                
                $makeTransfer = $this->bankTransfer($account, $amount, $code);

                if ($makeTransfer['status'] == false) {
                    $transaction->update([ 'status' => 'failed']);
                    return [
                        'error' => true,
                        'message' => 'Failed to complete transaction',
                    ];
                } else {
                    $bankTransfer->reference = $this->provider->getRefrence($makeTransfer);
                    $wallet->update([
                        'balance' => $wallet->balance - ($amount + $fee)
                    ]);
                }
            }

            if ($currency != 'NGN' || $request->b2b == true) {

                $validateAccount = validateAccount($request->account_bank, $request->account_number);

                if (!$validateAccount) {
                    return [
                        'error' => true,
                        'message' => 'Invalid account details',
                    ];
                }
            }

            $fiatPair = FiatRate::where('to', $getCurrency->id)->first();
            $converted_amount = $amount * $fiatPair->rate;
            $fee = getTxFee($converted_amount);
            
            $transaction->update([ 'amount' => $converted_amount, 'fee' => $fee ]);

            $wallet->update([
                'balance' => $wallet->balance - ($converted_amount + $fee)
            ]);
        }

        // event(new TransactionSuccess($transaction, $type));

        return [
            'error' => false,
            'message' => 'transfer was successful',
            'data' => [
                'transaction' => $transaction,
                'transfer' => $transfer,
            ]
        ];
    }


    /**
     * Performs Crypto Transfer
     */
    public function cryptoTransfer($request)
    {
        $user = User::whereId(auth()->user()->id)->first();
        $to = $request->address;
        $usd_amount = $request->amount_usd;
        $code = tx_code();
        $amountCrypto = $request->value;
        
        
        $asset = CryptoAsset::where('symbol', $request->currency)->firstOrFail();
        $fee = $asset->fee;

        $wallet = $user->wallets()->where('type', 'crypto')->whereAssetId($asset->id)->sole();

        $block = new BlockcypherService();
        $wallet->update([
            'balance' => $block->balance($wallet->address, $asset->symbol)['final_balance'],
        ]);

        if ($wallet->balance < $amountCrypto + $fee) {
            return [
                'error' => true,
                'message' => 'insufficient wallet balance'
            ];
        }

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'trxn_ref' => $code,
            'type' => 'debit',
            'amount' => $amountCrypto,
            'fee' => $fee,
        ]);

        $transactionMeta = TransactionMeta::create([
            'key' => 'reason',
            'value' => 'transfer crypto',
            'transaction_id' => $transaction->id
        ]);

        $createTransaction = $block->createTransaction($wallet->address, $to, $amountCrypto, $asset->symbol);

        if ($createTransaction['status'] == true) {
            $createRecord = CryptoRecord::create([
                'transaction_id' => $transaction->id,
                'asset_id' => $asset->id,
                'tx_hash' => $createTransaction['hash'],
                'block_height' => $createTransaction['block_height'],
                'tx_input_n' => $createTransaction['inputs']['addresses'][0],
                'tx_output_n' => $createTransaction['outputs']['addresses'][0],
                'value' => $amountCrypto,
                'ref_balance' => $wallet->balance,
            ]);

            $signature = (new Signer())->sign($createTransaction['tosign'], $wallet->private);

            $sendTransaction = $block->sendTransaction(strtolower($asset->symbol),$createTransaction['tosign'], $signature, $wallet->private);

            if ($sendTransaction['status'] == true) {

                $wallet->update([
                    'balance' => $block->balance($wallet->address, $asset->symbol)['final_balance'],
                ]);

                return [
                    'error' => false,
                    'message' => 'transaction completed',
                    'data' => [
                        'transaction' => $transaction,
                        'crypto_record' => $createRecord,
                    ]
                ];
            }

            return [
                'error' => true,
                'message' => 'could not complete transaction'
            ];
        }

    }
}



