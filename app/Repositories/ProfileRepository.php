<?php

namespace App\Repositories;

use App\Models\Bank;
use App\Interfaces\ProfileInterface;
use Illuminate\Support\Facades\Hash;

class ProfileRepository implements ProfileInterface {

    public function profileUpdate($request)
    {
        $user = auth()->user();
        $profile = $user->profile;

        if ($request->hasFile('avatar')) {
            $upload = uploadFile($request->file('avatar'), "images/".now()->format('Y')."/profile_pictures/");
            $user->update([
                'profile_photo_path' => $upload
            ]);
        }

        $profile->update([
            'firstname' => $request->firstname == 'undefined' ? null : $request->firstname,
            'lastname' => $request->lastname == 'undefined' ? null : $request->lastname,
        ]);
        return $profile;
    }


    public function changePassword($request)
    {
        $user = auth()->user();
        $checkLastPassword = Hash::check($request->old_password, $user->password);
        if ($checkLastPassword) {
            $new_password = Hash::make($request->password);
            $user->update(['password' => $new_password,]);
            return true;
        }
        return false;
    }

    public function addAccount($request)
    {
        $user = auth()->user();
        $banking = $user->bankingAccount;
        $bank = Bank::where('code', $request->bank)->first();

        $validate_account = validateAccount($request->account_number, $request->bank);
        if ($validate_account['status'] == true) {
            $account = $banking->create([
                'acc_number' => $request->account_number,
                'acc_name' => $validate_account['account_name'],
                'bank_id' => $request->bank,
            ]);
            return [
                'error' => false,
                'data' => $account
            ];
        }
        return ['error' => true, 'data' => null];
    }

    public function settingUpdate($request)
    {
        $user = auth()->user();
        $setting = $user->settings;

        if ($request->pin && $request->pin != null) {
            $oldPin = $request->prev_pin;
            $checkPin = Hash::check($oldPin, $setting->access_pin);

            if (!$checkPin) {
                return [
                    'error' => true,
                    'message' => 'Invalid access pin'
                ];
            }
            
            $setting->access_pin = Hash::make($request->pin);
        }

        $setting->notify_type = $request->notify_type;
        $setting->require_otp = $request->require_otp;

        $setting->save();

        
        return [
            'error' => false,
            'message' => 'Setting updated successfully!'
        ];
    }



}