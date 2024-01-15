<?php

namespace Roomies\Phonable\Contracts;

use Roomies\Phonable\Verification\VerificationRequest;
use Roomies\Phonable\Verification\VerificationResult;

interface VerifiesPhoneNumbers
{
    /**
     * Send the phone number verification code.
     */
    public function send(PhoneVerifiable $verifiable): VerificationRequest;

    /**
     * Attempt to verify the phone number code.
     */
    public function verify(PhoneVerifiable $verifiable, string $code): VerificationResult;
}
