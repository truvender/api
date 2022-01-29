<?php

namespace App\Repositories;

use App\Models\Faq;
use App\Models\Message;
use App\Models\Promotion;
use App\Events\NewMessage;
use App\Models\Conversation;
use App\Events\NewConversation;
use App\Jobs\SendContactMessage;
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

    public function contactSubmit($request)
    {
        SendContactMessage::dispatchAfterResponse([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
        ]);
        return true;
    }


    private function getConversation($conversation_id)
    {
        return Conversation::where('id', $conversation_id)
        ->where('closed', false)->first();
    }


    public function startConversation()
    {
        $conversation = Conversation::create([
            'user_id' => auth()->user()->id,
        ]);
        event( new NewConversation());

        return true;
    }


    public function endConversation($conversation_id)
    {
        $conversation = $this->getConversation($conversation_id);
        if ($conversation) {
            $conversation->update([
                'closed' => true
            ]);
            $conversation->delete();
        }
        return true;
    }


    public function acceptConversation($conversation_id)
    {
        $conversation = $this->getConversation($conversation_id);
        if($conversation){
            $conversation->update([
                'support_member' => auth()->user()->id
            ]);
        }
        $user = auth()->user();

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => "you are now chatting with $user->username",
            'type' => 1,
            'status' => 1,
        ]);

        broadcast(new NewMessage($user, $message))->toOthers();
        
        return true;
    }
    

    public function sendMessage($request)
    {
        $user = auth()->user();
        $conversation = Conversation::where('user_id', $user->id)
            ->where('closed', 'false')->first();
        $is_file = 1;

        if($request->hasFile('message')){
            $message = uploadFile($request->file('message'), now()->format('Y') . '/chat'); 
            $is_file = 2;
        }else{
            $message = $request->message;
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => $message,
            'type' => $is_file,
            'status' => 1,
        ]);

        broadcast(new NewMessage($user, $message))->toOthers();
        return true;
    }
}

