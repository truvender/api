<?php

namespace App\Interfaces;

interface CryptoInterface {

    public function listAssets();

    public function trade($request);
}