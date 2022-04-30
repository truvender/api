<?php

namespace App\Listeners;

use App\Events\KycApproval;
use App\Jobs\SendKycNotice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ReportKycApprovalStatus
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
     * @param  \App\Events\KycApproval  $event
     * @return void
     */
    public function handle(KycApproval $event)
    {
        $type = $event->type;
        $user = $event->user;
        SendKycNotice::dispatchAfterResponse($type, $user);
    }
}
