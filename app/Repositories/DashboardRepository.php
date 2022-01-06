<?php

namespace App\Repositories;

use App\Models\Bank;
use App\Interfaces\DashboardInterface;

class DashboardRepository implements DashboardInterface {


    /**
     * gte user dashboard data
     */
    public function userData()
    {
        $user = auth()->user();
        $profile = $user->profile;
        $settings = $user->settings;
        $wallets = $user->wallets;
        $role = $user->roles;
        $transactions = $user->transactions()->orderBy('created_at', 'desc')->with('metas')->get();

        return [
            'user' => $user,
            'transactions' => $transactions
        ];
    }

}