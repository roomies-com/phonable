<?php

namespace Roomies\Phonable\Contracts;

interface PhoneVerifiable
{
    /**
     * The verifiable phone number in E.164 format.
     */
    public function getVerifiablePhoneNumber(): ?string;

    /**
     * The current verification session identifier.
     */
    public function getVerifiableSession(): ?string;
}
