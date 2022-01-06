<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Traits\ApiResponse;
use App\Interfaces\AuthInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\{
    Login,
    Register,
    VerifyPhone,
    VerifyEmail,
    ResetPassword,
};
use App\Http\Traits\Jwt;

class Authentication extends Controller
{
    use ApiResponse;
    use Jwt;
    
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
     * Sign in @param Login $request
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


    /**
     * Resend Verification email @param Request $request
     */
    public function resendEmailVerification(Request $request)
    {
        try {

            $user = auth()->user();

            $resend_email = $this->interface->resendVerificationToken($user->email, 'mail');

            if (!$resend_email) {
                return $this->error('Invalid Email Address', 500, null);
            }

            return $this->success(null, 'Email Re-sent Successfully');
        } catch (\Throwable $err) {

            return $this->error($err->getMessage(), 500, null);
        }
    }


    /**
     * Verify email @param VerifyEmail $request
     */

    public function verifyEmail(VerifyEmail $request)
    {
        try {
            $verify_email = $this->interface->verifyEmail($request);

            if ($verify_email['error'] == false) {
                return $this->success($verify_email['data'], $verify_email['message']);
            }

            return $this->error($verify_email['message'], 401, null);
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500, null);
        }
    }


    /**
     * Resend Verification phone @param Request $request
     */
    public function resendPhoneVerification(Request $request)
    {
        try {
            $user = auth()->user();

            $resend_code = $this->interface->resendVerificationToken($user->phone, 'mobile');

            if ($resend_code) {
                return $this->success(null, 'Code Re-sent to mobile Successfully');
            }

            return $this->error('Invalid Phone', 500, null);
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500, null);
        }
    }


    /**
     * Verify phone 
     * @param VerifyPhone $request
     */

    public function verifyPhone(VerifyPhone $request)
    {
        try {
            $verify_phone = $this->interface->verifyPhone($request);

            if ($verify_phone['error'] && $verify_phone['error'] == true) {
                return $this->error($verify_phone['message'], 401, null);
            }else{
                return $this->success(null, $verify_phone['message']);
            }

        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500, null);
        }
    }

    /**
     * logout user
     */
    public function logout(Request $request)
    {
        try {

            $logout = $this->interface->logout();
            return $this->success(null, 'Authentication Revoked!');

        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500, null);
        }
    }


    /**
     * Forgot password request @param Request $request
     */
    public function forgotPassword(Request $request)
    {
        try {
            $forgot_password = $this->interface->forgotPassword($request->email);

            if ($forgot_password['error'] == false) {
                return $this->success(null, $forgot_password['message']);
            }

            return $this->error($forgot_password['message'], 401, null);
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500, null);
        }
    }


    /**
     * Reset Password @param ResetPassword $request
     */
    public function resetPassword(ResetPassword $request)
    {
        try {
            $reset_password = $this->interface->resetPassword($request);

            if ($reset_password['error'] == false) {
                return $this->success(null, $reset_password['message']);
            }

            return $this->error($reset_password['message'], 401, null);
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500, null);
        }
    }

    public function token_refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

}
