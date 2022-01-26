<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Traits\ApiResponse;
use App\Interfaces\BillInterface;
use App\Http\Requests\Bill\Airtime;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bill\Cable;
use App\Http\Requests\Bill\Data;

class Bills extends Controller
{
    use ApiResponse;

    public function __construct(BillInterface $interface)
    {
        $this->interface = $interface;
    }

    /**
     * airtime Purchase
     * @param \Http\Requests\Bill\Airtime $request
     * @return \Http\Traits\ApiResponse
     */
    public function buyAirtime(Airtime $request)
    {
        try {

            $purchaseAirtime = $this->interface->airtimePurchase($request);

            if ($purchaseAirtime['status'] == true) {
                return $this->success($purchaseAirtime, 'airtime purchase successful');
            }

            return $this->error($purchaseAirtime['message'], 500);
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }


    /**
     * data subscription
     * @param \Http\Requests\Bill\Data $request
     * @return \Http\Traits\ApiResponse
     */
    public function dataPurchase(Data $request)
    {
        try {

            $purchaseData = $this->interface->dataPurchase($request);

            if ($purchaseData['status'] == true) {
                return $this->success($purchaseData, 'airtime purchase successful');
            }

            return $this->error($purchaseData['message'], 500);
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }

    /**
     * cable subscription
     * @param \Http\Requests\Bill\Cable $request
     * @return \Http\Traits\ApiResponse
     */
    public function subscribeCable(Cable $request)
    {
        try {

            $subscribeCable = $this->interface->cableSubscription($request);

            if ($subscribeCable['status'] == true) {
                return $this->success($subscribeCable, 'airtime purchase successful');
            }

            return $this->error($subscribeCable['message'], 500);
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }

}
