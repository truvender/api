<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Interfaces\SupportInterface;
use App\Http\Requests\Support\AnswerQuestion;
use App\Http\Requests\Support\Contact;
use App\Http\Requests\Support\PromotionBanner;

class Support extends Controller
{
    use ApiResponse;


    public function __construct(SupportInterface $interface)
    {
        $this->interface = $interface;
    }

    /**
     * Create an answer to frequently asked Questions
     * @param AnswerQuestion $request
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

    /**
     * Add promotion banner
     * @param 
     */
    public function addBanner(PromotionBanner $request)
    {
        try {
            $promotion = $this->interface->addPromotion($request);
            return $this->success($promotion, 'request approved!');
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }


    /**
     * Send contact message to admin
     * @param Contact $request
     */
    public function sendContactMessage(Contact $request)
    {
        try {
            $this->interface->contactSubmit($request);
            return $this->success(true, 'request approved!');
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }

    /**
     * Start conversations
     * @param Request $request
     */
    public function startConversation(Request $request)
    {
        try {
            $start = $this->interface->startConversation();
            return $this->success(true, 'request approved!');
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }

    /**
     * End conversations
     * @param Request $request
     */
    public function endConversation(Request $request)
    {
        try {
            $request->validate([
                'conversation_id' => 'required'
            ]);
            $start = $this->interface->endConversation($request->conversation_id);
            return $this->success(true, 'request approved!');
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }


    /**
     * Accept conversations by a support team member
     * @param Request $request
     */
    public function acceptConversation(Request $request)
    {
        try {
            $request->validate([
                'conversation_id' => 'required'
            ]);
            $start = $this->interface->acceptConversation($request->conversation_id);
            return $this->success(true, 'request approved!');
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }

    /**
     * Accept conversations by a support team member
     * @param Request $request
     */
    public function newMessage(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required'
            ]);
            $start = $this->interface->sendMessage($request);
            return $this->success(true, 'request approved!');
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }
}
