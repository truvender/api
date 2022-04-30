<?php

namespace App\Repositories;

use App\Models\Bank;
use App\Models\User;
use App\Interfaces\ProfileInterface;
use Illuminate\Support\Facades\Hash;

class ProfileRepository implements ProfileInterface {

    public function profileUpdate($request)
    {
        $user = User::whereId(auth()->user()->id)->first();
        $profile = $user->profile;

        $profile->update([
            'firstname' => $request->firstname == 'undefined' ? null : $request->firstname,
            'lastname' => $request->lastname == 'undefined' ? null : $request->lastname,
        ]);
        return $profile;
    }


    public function uploadPhoto($request)
    {
        $user = User::whereId(auth()->user()->id)->first();
        if ($request->hasFile('avatar')) {
            $upload = uploadFile($request->file('avatar'), "images/" . now()->format('Y') . "/profile_pictures/");
            $user->update([
                'profile_photo_path' => $upload
            ]);

            return $upload;
        }
        return null;
    }


    public function changePassword($request)
    {
        $user = User::whereId(auth()->user()->id)->first();
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
        $user = User::whereId(auth()->user()->id)->first();
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
        $user = User::whereId(auth()->user()->id)->first();
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