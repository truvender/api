<?php 

namespace App\Repositories;

use App\Models\User;
use App\Models\Profile;
use App\Http\Traits\Jwt;
use App\Events\Verification;
use App\Interfaces\AuthInterface;
use App\Jobs\SendPasswordResetCode;
use App\Mail\PasswordresetMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;

class AuthRepository implements AuthInterface {

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

            $settings =  $user->settings()->create([
                'notify_type' => null,
                'require_otp' => false,
            ]);




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

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    /**
     * Logout user
     */
    public function logout()
    {
        $user = User::whereId(auth()->user()->id)->first();
        $user->update([
            'has_verify_otp' => false
        ]);
        
        auth()->logout();
        return true;
    }


    /**
     * Verify email address @param $request
     */
    public function verifyEmail($request)
    {
        $response = [];

        $email = $request->email;

        // Get the user Info
        $user = User::whereEmail($email)->first();
        if (!$user) {
            return $response[] = [
                'error' => true,
                'message' => 'Invalid Email Credentials',
            ];
        }

        // Check Token Exists
        $tokenExist = $user->tokens()->whereType('mail')->first();

        if (!$tokenExist) {
            return $response[] = [
                'error' => true,
                'message' => 'Token Not Valid',
            ];
        } else if ($tokenExist && $tokenExist->isExpired()) {
            return $response[] = [
                'error' => true,
                'message' => 'Verification token is expired',
            ];
        }

        // Check if Email has been verified before
        if ($user->email_verified_at != null) {
            return $response[] = [
                'error' => true,
                'message' => 'Email Verified Already',
            ];
        }

        $user->update([
            'email_verified_at' => now()
        ]);
        $tokenExist->delete();

        return $response[] = [
            'error' => false,
            'data' => $user,
            'message' => 'Email Address Verified successfully',
        ];
    }


    /**
     * resend verification 
     * @param string $to $type
     */
    public function resendVerificationToken($to, $type)
    {
        // Check User Exist
        if ($type == 'mobile') {
            $user = User::wherePhone($to)->first();
        }else{
            // Check User Exist
            $user = User::whereEmail($to)->first();
        }
        // Delete Old Token
        if ($user) {
            event(new Verification($user, $type));
            return true;
        }
        return false;
    }


    /**
     * Verify phone @param $data
     */
    public function verifyPhone($request)
    {
            $response = [];
            $phone = $request->phone;
            $token = $request->token;

            // Get the user Info
            $user = User::wherePhone($phone)->first();

            if (!$user) {
                return $response[] = [
                    'error' => true,
                    'message' => 'Invalid Credentials',
                ];
            }
            // Check Token Exists
            $tokenExist = $user->tokens()->where('type', 'mobile')->first();
            $verifyToken = verifySMSToken($tokenExist->code, $token);


            if (!$verifyToken) {
                return $response[] = [
                    'error' => true,
                    'message' => 'Invalid verification token',
                ];
            }else if($tokenExist && $tokenExist->isExpired()) {
                return $response[] = [
                    'error' => true,
                    'message' => 'Verification token is expired',
                ];
            }

            // Check if Email has been verified before
            if ($user->phone_verified_at != null) {
                return $response[] = [
                    'error' => true,
                    'message' => 'Phone has already been verified!',
                ];
            }

            $user->phone_verified_at = now();
            $user->save();
            // $tokenExist->delete();

            return $response[] = [
                'error' => false,
                'data' => $user,
                'message' => 'Phone Verified successfully',
            ];
    }

    /**
     * Password reset request
     * @param String $email
     */
    public function forgotPassword($email)
    {
        $response = [];
        $user = User::whereEmail($email)->first();

        if (!$user || $user == null) {
            return $response[] = [
                'error' => true,
                'message' => 'Invalid Email address',
            ];
        }else{
            
            SendPasswordResetCode::dispatchAfterResponse($user);
            return $response[] = [
                'error' => false,
                'message' => 'Password Instructions sent to Email',
            ];
        }

    }


    /**
     * Reset Password @param  $request
     */
    public function resetPassword($request)
    {
        $response = [];
        $user = User::whereEmail($request->email)->first();

        $tokenExist = $user->tokens()->whereType('mail')->whereCode($request->token)->first();

        if (!$user) {
            return $response[] = [
                'error' => true,
                'message' => 'Invalid Account',
            ];
        }

        if (!$tokenExist) {
            return $response[] = [
                'error' => true,
                'message' => 'Invalid Token',
            ];
        }else if($tokenExist && $tokenExist->isExpired()){
            return $response[] = [
                'error' => true,
                'message' => 'Verification token expired',
            ];
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        $tokenExist->delete();

        return $response[] = [
            'error' => false,
            'message' => 'Password reset Successfully',
        ];
    }





}