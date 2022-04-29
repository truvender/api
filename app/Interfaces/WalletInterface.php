<?php

namespace App\Interfaces;


interface WalletInterface {

    public function getWallets();

    public function getWallet($wallet_id);

    public function getTransaction($transaction_id);

    public function completeDefaultWalletFunding($request);

    public function fundDefaultWallet($request);

    public function transfer($request);

    public function cryptoTransfer($request);
    
}