<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Faq;
use App\Models\Bank;
use App\Models\Post;
use App\Models\Country;
use App\Models\FiatRate;
use App\Models\Variation;
use Illuminate\Http\Request;
use App\Http\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Promotion;

class GeneralState extends Controller
{
    use ApiResponse;



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

    private function getFiatRatesToNaira()
    {
        $pairs = FiatRate::all()->map(function ($pair) {
            return [
                'currency_from' => $pair->currencyFrom->code,
                'currency_to' => $pair->currencyTo->code,
                'min_amount' => $pair->min_amount,
                'rate' => $pair->rate,
            ];
        });
    }


    private function getPosts()
    {
        return Post::orderBy('created_at', 'desc')->get()->map->formatOutput();
    }


    public function promotions()
    {
        return Promotion::whereDate('expire_at', '>', now())->get();
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
                'countries' => Country::orderBy('name', 'asc')->get(),
                'fiat_rates' => $this->getFiatRatesToNaira(),
                'min_ngn_amount' => config('truvender.min_trx_ngn'),
                'bill_variations' => Variation::all(),
                'posts' => $this->getPosts(),
                'faqs' => Faq::all(),
                'promotions' => $this->promotions(),
                
            ], 'request approved!');
            
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500, null);
        }
       
    }
}
