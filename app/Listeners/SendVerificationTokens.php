<?php

namespace App\Listeners;

use App\Jobs\SendEmailVerificationToken;
use App\Jobs\SendPhoneVerificationToken;
use App\Jobs\SendWelcomeEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendVerificationTokens
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $user = $event->user;
        SendWelcomeEmail::dispatch($user)->delay(now()->addSeconds(20));
        SendEmailVerificationToken::dispatch($user)->delay(now()->addSeconds(20));
        SendPhoneVerificationToken::dispatch($user)->delay(now()->addSeconds(20));
    }
}
