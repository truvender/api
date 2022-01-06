<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class Countries extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $endpoint = "https://gist.githubusercontent.com/amitjambusaria/b9adebcb4f256eae3dfa64dc9f1cc2ef/raw/6431d72439c8399b05d2b8e9d51153e5dee7ad3b/countries.json";
        $request = Http::get($endpoint)->json();

        foreach ($request as $country) {

            $countryExists = Country::whereName($country['name'])->whereCode($country['code'])->first();

            if (!$countryExists) {
                $createCountry = Country::create([
                    'name' => $country['name'],
                    'code' => $country['code'],
                    'capital' => $country['capital'],
                    'region' => $country['region'],
                    'flag' => $country['flag'],
                    'currency_name' => $country['currency']['name'],
                    'currency_symbol' => $country['currency']['symbol'],
                    'currency_code' => $country['currency']['code'],
                ]);
            }
        }

        $endpoint = "https://gist.githubusercontent.com/Goles/3196253/raw/9ca4e7e62ea5ad935bb3580dc0a07d9df033b451/CountryCodes.json";
        $request = Http::get($endpoint)->json();

        foreach ($request as $value) {
           $country = Country::whereCode($value['code'])->first();
           if ($country) {
               $country->update([
                    'dial_code' => $value['dial_code']
               ]);
           }
        }
    }
}
