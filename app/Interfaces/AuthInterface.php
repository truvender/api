<?php

namespace App\Interfaces;

interface AuthInterface {

    public function register($request);

    public function login($request);
   
}