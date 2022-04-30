<?php

namespace App\Helpers;

class Constants {

    public const REQUIRED_IMAGE_VALIDATION = "required|mimes:png,jpg,svg|size:10240";
    public const IMAGE_VALIDATION = "nullable|mimes:png,jpg,svg|size:10240";
    
}