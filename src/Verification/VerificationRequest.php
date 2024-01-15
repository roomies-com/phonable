<?php

namespace Roomies\Phonable\Verification;

readonly class VerificationRequest
{
    /**
     * Create a new verification request instance.
     */
    public function __construct(public mixed $id, public string $phoneNumber)
    {
        //
    }
}
