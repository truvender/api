<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use App\Events\{
    Verification,
    KycApproval,
    NewConversation
};
use App\Listeners\{
    AssignRoleToUser,
    CreateUserWallets,
    SendVerificationTokens,
    ResendVerificationToken,
    ReportKycApprovalStatus,
    SendNotificationsToSupportTeam,
};
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            AssignRoleToUser::class,
            CreateUserWallets::class,
            SendVerificationTokens::class,
            GenerateAccountNumber::class,
        ],
        Verification::class => [
            ResendVerificationToken::class,
        ],
        NewConversation::class => [
            SendNotificationsToSupportTeam::class
        ],
        KycApproval::class => [
            ReportKycApprovalStatus::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
