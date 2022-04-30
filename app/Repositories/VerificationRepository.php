<?php

namespace App\Repositories;

use App\Events\KycApproval;
use App\Interfaces\VerificationInterface;
use App\Models\Kyc;
use App\Models\User;

class VerificationRepository implements VerificationInterface {

    public function authenticatedUser()
    {
        return User::whereId(auth()->user()->id)->first();
    }

    public function submitDocument($request)
    {
        $user = $this->authenticatedUser();
        $previousRequest = Kyc::whereUserId($user->id)->sole();
        $document = uploadFile($request->file('document'), '/kyc/requests');
        if($previousRequest == true){
            $previousRequest->update([
                'document' => $document,
                'type' => $request->type,
                'name' => $request->name,
                'date_of_birth' => $request->date_of_birth
            ]);
        }else{
            $createRequest = Kyc::create([
                'user_id' => $user->id,
                'document' => $document,
                'type' => $request->type,
                'name' => $request->name,
                'date_of_birth' => $request->date_of_birth
            ]);
        }
        return true;
    }

    public function getStatus()
    {
        $user = $this->authenticatedUser();
        $openRequest = Kyc::whereUserId($user->id)->sole();
        return $openRequest->approved;
    }


    public function approve($request_id)
    {
        $request = Kyc::whereId($request_id)->first();
        $request->update([
            'approve' => true
        ]);
        event(new KycApproval('approved'));
        return true;
    }

    public function disapprove($request_id)
    {
        $request = Kyc::whereId($request_id)->first();
        $request->update([
            'approve' => false
        ]);
        event(new KycApproval('disapproved'));
        return true;
    }


    public function getAllRequests()
    {
        return Kyc::orderBy('created_at', 'desc')->with('user')->map(function($request){
            $user = User::whereId($request->user_id)->first();
            return [
                'user' => [
                    'name' => $user->profile->firstname . ' ' . $user->profile->lastname,
                    'email' => $user->email,
                    'username' => $user->username,
                    'profile_photo_path' => $user->profile_photo_path,
                ],
                'name' => $request->name,
                'document' => $request->document,
                'type' => $request->type,
                'date_of_birth' => $request->date_of_birth,
                'approved' => $request->approved,
                'created_at' => $request->created_at
            ];
        });
    }
}