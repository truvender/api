<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Traits\ApiResponse;
use App\Interfaces\CardInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Card\AddCard;

class GiftCards extends Controller
{
    use ApiResponse;

    public function __construct(CardInterface $interface)
    {
        $this->interface = $interface;
    }


    /**
     * List all giftcards
     */
    public function listGiftCards()
    {
        try {
            $cards = $this->interface->listCards();
            return $this->success($cards, 'request approved!');
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }



    /**
     * Add a new gitcard
     * @param AddCard $request
     */
    public function createCard(AddCard $request)
    {
        try {
            
            $card = $this->interface->addCard($request);
            return $this->success($card, 'request approved!');

        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }
}
