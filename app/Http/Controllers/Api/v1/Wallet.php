<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Traits\ApiResponse;
use App\Interfaces\WalletInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\Deposit;
use App\Http\Requests\Wallet\Transfer;
use App\Http\Requests\Wallet\DepositRequest;

class Wallet extends Controller
{
    use ApiResponse;

    public function __construct(WalletInterface $interface)
    {
        $this->interface = $interface;
    }


    /**
     * initiate default wallet Deposit
     * @param DepositRequest $request
     */
    public function nairaFund(DepositRequest $request)
    {
        try {
            $initiateDeposit = $this->interface->fundDefaultWallet($request);
            return $this->success($initiateDeposit, 'request approved!');
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }


    /**
     * complete default wallet Deposit
     * @param Deposit $request
     */
    public function completeFund(Deposit $request)
    {
        try {
            $fundWallet = $this->interface->completeDefaultWalletFunding($request);
            return $this->success($fundWallet, 'request approved!');
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }


    /**
     * Transfer or withdraw fund
     * @param Transfer $request
     */
    public function transfer(Transfer $request)
    {
        try {
            $transferFund = $this->interface->transfer($request);
            return $this->success($transferFund, 'request approved!');
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }
}
