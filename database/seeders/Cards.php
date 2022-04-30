<?php

namespace Database\Seeders;

use App\Models\Card;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
        $cards = DB::connection('mysql_20')->table('cards')->get();
        
        foreach ($cards as $oldCard) {
            $newCard = Card::create([
                'name' => $oldCard->name,
                'image' => $oldCard->image
            ]);
        }
    }
}