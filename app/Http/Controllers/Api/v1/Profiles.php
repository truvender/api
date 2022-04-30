<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\Constants;
use Illuminate\Http\Request;
use App\Http\Traits\ApiResponse;
use App\Http\Requests\User\{
    Banking,
    Profile,
    Settings,
};
use App\Http\Controllers\Controller;
use App\Interfaces\ProfileInterface;

class Profiles extends Controller
{
    use ApiResponse;


    public function __construct(ProfileInterface $interface)
    {
        $this->interface = $interface;
    }

    /**
     * update user profile
     * @param Profile $request
     */
    public function updateProfile(Profile $request): object
    {
        try {
            $updateProfile = $this->interface->profileUpdate($request);
            return $this->success($updateProfile, 'profile updated');
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }

    /**
     * Update Profile avatar
     * @param Request $request
     */
    public function uploadAvatar(Request $request)
    {
        try {
            $request->validate([ 'avatar' => Constants::REQUIRED_IMAGE_VALIDATION ]);
            $avatar = $this->interface->uploadPhoto($request);
            return $this->success($avatar, 'request approved!');
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }

    

    /**
     * add banking account details
     * @param Banking $request
     */
    public function addAccount(Banking $request)
    {
        try {
            $account = $this->interface->addAccount($request);

            if ($account['error'] == true) {
                return $this->error('invalid account datils', 500);
            }

            return $this->success($account, 'account added successful');

        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }

    /**
     * change user settings
     * @param Settings $request
     */
    public function changeSettings(Settings $request)
    {
        try {
            $setting = $this->interface->settingUpdate($request);


            if ($setting['error'] == true) {
                return $this->error($setting['message'], 500);
            }
            return $this->success($setting, 'updated setting successfully!');
            
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }
}
