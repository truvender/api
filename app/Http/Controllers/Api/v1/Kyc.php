<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Verification\SubmitDoc;
use App\Http\Traits\ApiResponse;
use App\Interfaces\VerificationInterface;
use Illuminate\Http\Request;

class Kyc extends Controller
{
    use ApiResponse;

    public function __construct(VerificationInterface $interface)
    {
        $this->interface = $interface;
    }

    /**
     * Get all kyc requests
     * @param
     */
    public function getRequests()
    {
        try {
            $requests = $this->interface->getAllRequests();
            return $this->success($requests, 'request approved!');
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }

    /**
     * Submit Verification document
     * @param SubmitDoc $request
     * @return
     */
    public function submitKycDoc(SubmitDoc $request)
    {
        try {
            $kyc = $this->interface->submitDocument($request);
            return $this->success(null, 'document submitted and awaiting approval');
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }

    /**
     * Submit Verification document
     * @param SubmitDoc $request
     * @return
     */
    public function getStatus()
    {
        try {

            $kycStatus = $this->interface->getStatus();
            return $this->success($kycStatus, 'request approved!');

        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }

    /**
     * Submit Verification document
     * @param string $request_id
     */
    public function approveKycRequest($request_id)
    {
        try {
            $approval = $this->interface->approve($request_id);
            return $this->success($approval, 'request approved!');
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }

    /**
     * Submit Verification document
     * @param string $request_id
     * @return
     */
    public function disapproveKycRequest($request_id)
    {
        try {
            $approval = $this->interface->disapprove($request_id);
            return $this->success($approval, 'request approved!');
        } catch (\Throwable $err) {
            return $this->error($err->getMessage(), 500);
        }
    }
}
