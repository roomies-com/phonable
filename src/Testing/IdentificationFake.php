<?php

namespace Roomies\Phonable\Testing;

use Illuminate\Support\Testing\Fakes\Fake;
use Roomies\Phonable\Contracts\IdentifiesPhoneNumbers;
use Roomies\Phonable\Contracts\PhoneIdentifiable;
use Roomies\Phonable\Identification\IdentificationResult;

class IdentificationFake implements Fake, IdentifiesPhoneNumbers
{
    /**
     * The phone verification results that have been received.
     */
    protected array $responses = [];

    /**
     * Create a new fake instance.
     */
    public function __construct(protected array $identificationsToFake = [])
    {
        foreach ($identificationsToFake as $key => $result) {
            $this->responses[$key] = $result;
        }
    }

    /**
     * Do not change the driver if requested.
     */
    public function driver(string $driver)
    {
        return $this;
    }

    /**
     * Fetch the identification for the given phone number.
     */
    public function handle(PhoneIdentifiable $identifiable): ?IdentificationResult
    {
        return $this->responses[$identifiable->getIdentifiablePhoneNumber()] ?? null;
    }
}
