<?php

namespace Roomies\Phonable\Contracts;

interface PhoneIdentifiable
{
    /**
     * The identifiable phone number in E.164 format.
     */
    public function getIdentifiablePhoneNumber(): ?string;
}
