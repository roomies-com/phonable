<?php

namespace Roomies\Phonable\Tests\Identification;

use Roomies\Phonable\Contracts\PhoneIdentifiable;

class Identifiable implements PhoneIdentifiable
{
    public function __construct(
        public ?string $phoneNumber = '+12125550000',
    ) {
        //
    }

    public function getIdentifiablePhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }
}
