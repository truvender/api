<?php

namespace App\Jobs;

use App\Models\MobileList;
use App\Models\Token;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendPhoneVerificationToken implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->user;

        $phone = cleanPhone($user->phone);
        $sendToken = sendSMSToken($phone);

        $checkPhoneInMobileList = MobileList::where('phone', $phone)->first();
        if(!$checkPhoneInMobileList){
            MobileList::create([
                'user_id' => $user->id,
                'phone' => $phone
            ]);
        }
        
        $token = Token::whereUserId($user->id)->where('type', 'mobile')->get();
        $token->each->delete();

        // Save Email Token
        Token::create([
            'user_id' => $user->id,
            'code' => $sendToken['pinId'],
            'type' => 'mobile',
            'expire_at' => now()->addMinutes(5)
        ]);
    }
}
