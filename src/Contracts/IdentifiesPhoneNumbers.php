<?php

namespace Roomies\Phonable\Contracts;

use Roomies\Phonable\Identification\IdentificationResult;

interface IdentifiesPhoneNumbers
{
    /**
     * Fetch the identification for the given phone number.
     */
    public function get(PhoneIdentifiable $identifiable): ?IdentificationResult;
}
