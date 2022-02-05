<?php 

namespace App\Repositories;

use App\Models\Card;
use App\Models\Country;
use App\Models\CardType;
use App\Models\CardPrice;
use App\Interfaces\CardInterface;
use App\Models\CardRate;
use App\Models\DefaultRate;
use App\Models\TradeRange;

class CardRepository implements CardInterface {

    public function addCard($request)
    {
        $upload = uploadFile($request->file('image'), now()->format('Y') .'/gitcards');
        
        $card = Card::create([
            'name' => $request->name,
            'image' => $upload
        ]);
    }

    private function getCard($card_id)
    {
        return Card::where('id', $card_id)->sole();
    }

    private function getType($type_id)
    {
        return CardType::where('id', $type_id)->sole();
    }

    public function updateCard($request)
    {
        $card = $this->getCard($request->card);
    }

    public function listCards()
    {
        $giftcards = Card::orderBy('name', 'asc')->with(['types', 'countries', 'prices', 'rates', 'default_rates'])->get()->map(function($card){

            return [
                'id' => $card->id,
                'name' => $card->name,
                'image' => $card->image,
                'types' => $card->types,
                'countries' => $card->countries,
                'prices' => $card->prices,
                'rates' => $card->rates,
                'default_rates' => $card->default_rates()->with('trade_range')->get(),
            ];
        });

        return $giftcards;
    }


    public function updateRate($request)
    {
        $rate = CardRate::where('id', $request->rate)->sole();
        $rate->update([
            'buyer_rate' => $request->buyer_rate,
            'seller_rate' => $request->seller_rate,
        ]);

        return true;
    }


    public function addRate($request)
    {
        $card = $this->getCard($request->card);
        $type = $this->getType($request->type);
        $country = Country::where('id', $request->country)->first();
        $price = CardPrice::where('card_id', $card->id)
            ->where('card_type_id', $type->id)->where('amount', $request->amount)->first();
        if(!$price){
            CardPrice::create([
                'card_id' => $card->id,
                'type_id' => $type->id,
                'amount' => $request->amount
            ]);
        }

        $rate = CardRate::create([
            'card_id' => $card->id,
            'country_id' => $country->id,
            'type_id' => $type->id,
            'price_id' => $price->id,
            'buyer_rate' => $request->buyer_rate,
            'seller_rate' => $request->seller_rate,
        ]);

        // if(count($request->default_rate_buyer) > 0){
        //     $default = DefaultRate::where('card_id', $card->id)
        //         ->where('country_id', $rate->country_id)->where('type_id', $type->id)->first();
        // }

        return $rate;
    }


    public function deleteRate($rate_id)
    {
        $rate = CardRate::where('id', $rate_id)->sole();
        $rate->delete();
        return true;   
    }


    public function trade($request)
    {
        $card = Card::where('id', $request->card)->with(['types', 'prices', 'default_rates', 'rates', 'countries'])->first();
        $country = $card->countries()->where('id', $request->country)->first();
        $type = $card->types()->where('id', $request->type)->first();
        $quantity = $request->quantity;
        
        $price = $card->prices()->where('id', $request->price)->first();
        $rate = $card->rates()->where('id', $request->rate)
            ->where('country_id', $country->id)->where('type_id', $type->id)
                ->where('price_id', $price->id)->first();
        if($rate){
            $buyer_rate = $rate->buyer_rate;
            $seller_rate = $rate->seller_rate;
        }else{
            $range = TradeRange::where('minimum', '<=', $price->amount)->where('maximum', '>=', $price->amount)->first();
            $default = $card->default_rates()
                ->where('country_id', $country->id)
                ->where('range_id', $range->id)
                ->where('type_id', $type->id)->first();
        }

    }
    
}