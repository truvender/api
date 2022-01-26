<?php

namespace App\Interfaces;

interface BillInterface {

    public function airtimePurchase($request);

    public function purchaseData($request);

    public function cableSubscription($request);
}