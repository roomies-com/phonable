<?php

namespace Roomies\Phonable\Identification;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Roomies\Phonable\Contracts\IdentifiesPhoneNumbers;
use Roomies\Phonable\Contracts\PhoneIdentifiable;
use Vonage\Client;
use Vonage\Client\Exception\Request as RequestException;
use Vonage\Insights\StandardCnam;

class Vonage implements IdentifiesPhoneNumbers
{
    /**
     * Create a new instance.
     */
    public function __construct(protected Client $vonage)
    {
        //
    }

    /**
     * Fetch the identification for the given phone number.
     */
    public function get(PhoneIdentifiable $identifiable): ?IdentificationResult
    {
        $method = Str::startsWith($identifiable->getIdentifiablePhoneNumber(), '+1')
            ? 'standardCnam'
            : 'standard';

        try {
            $insight = $this->vonage->insights()
                ->{$method}($identifiable->getIdentifiablePhoneNumber());
        } catch (RequestException $exception) {
            return null;
        }

        $carrier = $insight->getCurrentCarrier();

        return new IdentificationResult(
            carrierName: Arr::get($carrier, 'name'),
            carrierCountry: Arr::get($carrier, 'country'),
            networkType: Arr::get($carrier, 'network_type'),
            callerName: $insight instanceof StandardCnam ? $insight->getCallerName() : null,
            callerType: $insight instanceof StandardCnam ? $insight->getCallerType() : null,
            data: $insight,
        );
    }
}
