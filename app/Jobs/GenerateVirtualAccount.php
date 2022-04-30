<?php

namespace App\Jobs;

use App\Models\VirtualAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class GenerateVirtualAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;
    /**
     * Create a new  account number for user.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $total_accounts = VirtualAccount::count();
        $data = $this->data;
        $user = auth()->user();
        $base_url = config('services.flutterwave.root-url');
        $endpoint = $base_url. 'virtual-nubans';
        $key = config('services.flutterwave.secrete_key');

        $request = Http::withHeaders(['Authorization' => 'Bearer ' . $key])->post($endpoint,[
            'email' => $user->email,
            "tx_ref" => "TVA" . $total_accounts + 1,
            "is_permanent" => true,
            "bvn" => $data['verification_string'],
            'narration' => $user->username.'@truvender.com',
        ])->json();

        if($request['status'] == 'success' && $request['data'] != null){
            $data = $request['data'];

            $virtual_account = VirtualAccount::create([
                'user_id' => $user->id,
                'bank' => $data['bank_name'],
                'account_number' => $data['account_number'],
                'account_name' => $data['note'],
                'order_ref' => $data['order_ref'],
                'amount' => $data['amount']
            ]);
        }
        
    }
}
