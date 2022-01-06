<?php

namespace App\Listeners;

use App\Events\Verification;
use App\Jobs\SendEmailVerificationToken;
use App\Jobs\SendPhoneVerificationToken;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResendVerificationToken
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
     * @param  \Illuminate\Auth\Events\Verification  $event
     * @return void
     */
    public function handle(Verification $event)
    {
        if ($event->type == 'mail') {
            SendEmailVerificationToken::dispatch($event->user)->delay(now()->addSeconds(10));
        }else{
            SendPhoneVerificationToken::dispatch($event->user)->delay(now()->addSeconds(10));
        }
    }
}
