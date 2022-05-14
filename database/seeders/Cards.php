<?php

namespace Database\Seeders;

use App\Models\Card;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Cards extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Card::truncate();

        DB::transaction(function () {

            // $access_key = config('services.xoxo.access');

            // $xoxoData = Http::withHeaders(['Authorization' => "Bearer " . $access_key])
            // ->post(config('services.xoxo.url'), [
            //     "query" => "plumProAPI.mutation.getVouchers",
            //     "tag" => "plumProAPI",
            //     "variables" => [
            //         "data" => [
            //             "limit" => 0,
            //             "page" => 0
            //         ]
            //     ]
            // ])->json();

            // $getVouchers = $xoxoData['data']['getVouchers'];

            // if ($getVouchers['status'] == 1) {
            //     foreach ($getVouchers['data'] as $voucher) {
            //         // $getCard = Card::where('name', $voucher['name'])->first();
            //         $getCard = DB::connection('mysql_20')->table('cards')->where('name',$voucher['name'])->first();
                    
            //         if ($getCard) {
            //             $card = Card::create([
            //                 'product_id' => $voucher['productId'],
            //                 'description' => $voucher['description'],
            //                 'name' => $voucher['name'],
            //                 'image' => $voucher['imageUrl'],
            //                 'is_available' => $getCard->is_available ? true : false,
            //             ]);
            //         }
            //     }
            // }
            
            $cards = DB::connection('mysql_20')->table('cards')->get();
            foreach ($cards as $card){
                Card::create([
                    'name' => $card->name,
                    'image' => $card->image,
                    'is_available' => $card->is_available,
                ]);
            }
        });
        
    }
}