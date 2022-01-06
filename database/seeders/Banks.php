<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class Banks extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $key = \config('services.flutterwave-test.secrete_key');


        Bank::truncate();
        $countries = [
            'NG',
            'GH',
            'KE',
            'UG',
            'ZA',
            'TZ'
        ];


        foreach ($countries as $country) {
            $endpoint = 'https://api.flutterwave.com/v3/banks/' . $country;
            $api_call = Http::withHeaders(['Authorization' => $key])->get($endpoint)->json();
            $banks = $api_call['data'];
            $country = Country::whereCode($country)->first();

            if($country){
                
                foreach ($banks as $bank) {
                    Bank::create([
                        'name' => $bank['name'],
                        'code' => $bank['code'],
                        'country_id' => $country->id,
                    ]);
                }
            }

        }
    }
}
