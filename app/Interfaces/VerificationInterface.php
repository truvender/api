<?php

namespace App\Interfaces;

interface VerificationInterface {

    public function submitDocument($request);

    public function getStatus();

    public function approve($request_id);

    public function disapprove($request_id);

    public function getAllRequests();

}