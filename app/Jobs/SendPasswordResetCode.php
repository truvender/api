<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Mail\PasswordresetMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendPasswordResetCode implements ShouldQueue
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
        $code = generatePasswordResetCode();
        
        $tokens = $user->tokens()->where('type', 'mail')->get();
        $tokens->each->delete();

        // Save the reset code to db
        $token = $user->tokens()->create([
            'code' => $code,
            'type' => 'mail',
            'expire_at' => now()->addMinutes(5)
        ]);

        // send mail to user with the reset password token
        Mail::to($user)->send(new PasswordresetMail($user, $token));
    }
}
