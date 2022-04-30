<?php

namespace App\Interfaces;

interface ProfileInterface {

    public function profileUpdate($request);

    public function changePassword($request);

    public function addAccount($request);

    public function settingUpdate($request);

    public function uploadPhoto($request);


}