<?php

namespace App\Jobs;

use App\Models\Token;
use Illuminate\Bus\Queueable;
use App\Mail\VerificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendEmailVerificationToken implements ShouldQueue
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
        $code = generateToken();

        // Save Email Token
        $token = Token::create([
            'user_id' => $user->id,
            'code' => $code,
            'type' => 'mail',
            'expire_at' => now()->addMinutes(5)
        ]);
        
        Mail::to($user)->send(new VerificationMail($user, $token));
    }
}
