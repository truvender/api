<?php 

namespace App\Repositories;

use App\Http\Traits\Jwt;
use App\Models\User;
use App\Models\Profile;
use App\Interfaces\AuthInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthInterface {

    use Jwt;

    /**
     * Register user @param request
     */
    public function register($request)
    {
        DB::transaction(function () use ($request) {

            //Check Referrer
            $referrer =
                $request->referrer_code ?
                User::where('username', $request->referrer_code)->first()
                : null;

            // Create user
            $user = User::create([
                'referrer_id' => $referrer == true ? $referrer->id : null,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            //Profile
            $profile = Profile::create([
                'user_id' => $user->id
            ]);

            $settings =  $user->setting()->create([
                'notify_type' => null,
                'require_otp' => false,
            ]);

            // Token
            $token = $user->createToken('auth_token')->plainTextToken;

            event(new Registered($user));
        });
        return true;
    }


    public function login($request)
    {
        $credentials = $request->only('username', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return $response = [
                'error' => true,
                'message' => 'Invalid login credentials'
            ];
        }

        $user = User::where('username', $request['username'])->firstOrFail();

        return $this->respondWithToken($token);
    }

}