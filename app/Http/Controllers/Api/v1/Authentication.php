<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Traits\ApiResponse;
use App\Interfaces\AuthInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\{
    Login,
    Register
};

class Authentication extends Controller
{
    use ApiResponse;
    
    public function __construct(AuthInterface $interface)
    {
        $this->interface = $interface;
    }

    /**
     * Sign up @param Register $request
     */
    public function register(Register $request)
    {
        try {

            $createUser = $this->interface->register($request);

            if (!$createUser) {
                return $this->error('Sign up failed. Please try again', 500, null);
            }

            return $this->success($createUser, 'Sign up was Successful', 201);
        } catch (\Throwable $err) {
            
            return $this->error($err->getMessage(), 500, null);
        }
    }



    /**
     * Sign in @param SigninRequest $request
     */
    public function login(Login $request)
    {
        try {
            $login = $this->interface->login($request);

            if (array_key_exists('error', $login) && $login['error'] == true) {
                return $this->error($login['message'], 500, null);
            }

            return response()->json($login, 201);

        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500, null);
        }
    }

}
