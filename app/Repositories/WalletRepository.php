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
use App\Models\Transaction;
use App\Models\BankTransfer;
use App\Models\TransactionMeta;
use Illuminate\Support\Facades\DB;
use App\Interfaces\WalletInterface;

class WalletRepository implements WalletInterface {

    public function getWallet($wallet_id)
    {
        return Wallet::whereId($wallet_id)->with('transactions')->first();
    }

    public function getTransaction($transaction_id)
    {
        return Transaction::whereId($transaction_id)->with('metas')->first();
    }

    
    public function fundDefaultWallet($request)
    {
        return DB::transaction(function () use ($request) {
            $user = auth()->user();
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
            $user = auth()->user();

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



    public function transfer($request)
    {
        $user = auth()->user();
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
            'value' => 'transfer',],
            ['key' => 'transfer_type',
            'value' => $type]
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
}



