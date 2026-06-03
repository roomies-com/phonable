<?php

namespace Roomies\Phonable\Contracts;

use Roomies\Phonable\Verification\VerificationMethod;
use Roomies\Phonable\Verification\VerificationRequest;
use Roomies\Phonable\Verification\VerificationResult;

interface VerifiesPhoneNumbers
{
    /**
     * Send the phone number verification code.
     */
    public function send(string|PhoneVerifiable $verifiable, VerificationMethod $method = VerificationMethod::Automatic): VerificationRequest;

    /**
     * Attempt to verify the phone number code.
     */
    public function verify(string|PhoneVerifiable $verifiable, string $code): VerificationResult;
}
