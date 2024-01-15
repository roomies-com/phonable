<?php

namespace Roomies\Phonable\Identification;

readonly class IdentificationResult
{
    /**
     * Create a new insights instance.
     */
    public function __construct(
        public ?string $carrierName = null,
        public ?string $carrierCountry = null,
        public ?string $networkType = null,
        public ?string $callerName = null,
        public ?string $callerType = null,
        public mixed $data = null,
    ) {
        //
    }
}
