<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Interfaces\DashboardInterface;

class Dashboards extends Controller
{
    use ApiResponse;

    public function __construct(DashboardInterface $interface)
    {
        $this->interface = $interface;
    }


    /**
     * Get data for authenticated user
     */
    public function userSessionData()
    {
        try {
            $data = $this->interface->userData();
            return $this->success($data, 'request approved!');

        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500, null);
        }   
    }
}
