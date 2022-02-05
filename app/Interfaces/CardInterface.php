<?php

namespace App\Interfaces;

interface CardInterface {

    public function listCards();

    public function addCard($request);

    public function updateCard($request);

    public function updateRate($request);

    public function addRate($request);

    public function deleteRate($rate_id);
    
}