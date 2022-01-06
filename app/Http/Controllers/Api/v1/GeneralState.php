<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Bank;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Traits\ApiResponse;
use App\Http\Controllers\Controller;

class GeneralState extends Controller
{
    use ApiResponse;

    /**
     * get all countries
     */
    private function getCountries()
    {
        return $countries = Country::orderBy('name', 'asc')->get();
    }

    /**
     * get all banks
     */
    private function getBanks()
    {
        return $banks = Bank::orderBy('name', 'asc')->get()->map(function ($bank) {
            return [
                'name' => $bank->name,
                'code' => $bank->code,
                'country' => $bank->country->code,
            ];
        });
    }


    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        try {
            return $this->success([
                'banks' => $this->getBanks(),
                'countries' => $this->getCountries(),
            ], 'request approved!');
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500, null);
        }
       
    }
}
