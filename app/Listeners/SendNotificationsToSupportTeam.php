<?php

namespace App\Listeners;

use App\Models\Role;
use App\Models\User;
use App\Events\NewConversation;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\SendConversationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNotificationsToSupportTeam
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
     * @param  \App\Events\NewConversation  $event
     * @return void
     */
    public function handle(NewConversation $event)
    {
        $user = auth()->user();

        $support_team = Role::where('name', 'support')->with('users')->first();

        foreach ($support_team->users as $member ){
            SendConversationNotification::dispatch($member)->delay(now()->addSeconds(10));
        }
        
    }
}
