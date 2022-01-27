<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Interfaces\SupportInterface;
use App\Http\Requests\Support\AnswerQuestion;

class Support extends Controller
{
    use ApiResponse;


    public function __construct(SupportInterface $interface)
    {
        $this->interface = $interface;
    }

    /**
     * Create an answer to frequently asked Questions
     */
    public function answerQuestion(AnswerQuestion $request)
    {
        try {
            $storeAnswer = $this->interface->answerQuestion($request);
            return $this->success($storeAnswer, 'request approved!');
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }
}
