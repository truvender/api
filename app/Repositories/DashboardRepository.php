<?php

namespace App\Repositories;

use App\Models\User;
use App\Interfaces\DashboardInterface;

class DashboardRepository implements DashboardInterface {


    /**
     * gte user dashboard data
     */
    public function userData()
    {
        $user = User::whereId(auth()->user()->id)->first();
        $profile = $user->profile;
        $settings = $user->settings;
        $wallets = $user->wallets;
        $role = $user->roles()->pluck('name')->get();
        $transactions = $user->transactions()->orderBy('created_at', 'desc')->with('metas')->get();
        $banking_details = $user->bankingAccount;

        $max_ngn_amount = 0; 

        $data = [
            'user' => $user,
            'transactions' => $transactions,
            'banking_details' => $banking_details
        ];

        if ($user->posts) {
            $data['posts'] = $user->posts()->map->formatOutput();
        }
    
        return $data;
    }

}