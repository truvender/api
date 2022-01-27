<?php

namespace App\Repositories;

use App\Models\Faq;
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

    
}

