<?php

namespace App\Repositories;

use App\Models\Faq;
use App\Models\Promotion;
use App\Interfaces\SupportInterface;

class SupportRepository implements SupportInterface {


    public function answerQuestion($request)
    {
        $answer = Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
        ]);

        return true;
    }

    public function addPromotion($request)
    {
        $upload = uploadFile($request->file('image'), now()->format('Y') .'/promotions');

        $promotion = Promotion::create([
            'name' => $request->name,
            'image' => $upload,
            'description' => $request->description,
            'expires_at' => now()->parse($request->expires_date),
        ]);
        
        return true;
    }
    
}

