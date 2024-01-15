<?php

namespace Roomies\Phonable\Tests\Verification;

use Roomies\Phonable\Contracts\PhoneVerifiable;

class Verifiable implements PhoneVerifiable
{
    public function __construct(
        public ?string $phoneNumber = '+12125550000',
        public ?string $sessionId = null,
    ) {
        //
    }

    public function getVerifiablePhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function getVerifiableSession(): ?string
    {
        return $this->sessionId;
    }
}
