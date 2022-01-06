<?php

namespace App\Interfaces;

interface AuthInterface {

    public function register($request);

    public function login($request);
    
    public function logout();

    public function verifyEmail($request);

    public function resendVerificationToken($to, $type);

    public function verifyPhone($request);

    public function forgotPassword($email);

    public function resetPassword($request);
   
}