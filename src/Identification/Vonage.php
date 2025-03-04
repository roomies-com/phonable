<?php

namespace Roomies\Phonable\Identification;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Roomies\Phonable\Contracts\IdentifiesPhoneNumbers;
use Roomies\Phonable\Contracts\PhoneIdentifiable;
use Vonage\Client;
use Vonage\Client\Exception\Request as RequestException;
use Vonage\Insights\Standard;
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
    public function get(string|PhoneIdentifiable $identifiable): ?IdentificationResult
    {
        $phoneNumber = $identifiable instanceof PhoneIdentifiable
            ? $identifiable->getIdentifiablePhoneNumber()
            : $identifiable;

        $method = Str::startsWith($phoneNumber, '+1')
            ? 'standardCnam'
            : 'standard';

        try {
            $insight = $this->vonage->insights()
                ->{$method}($phoneNumber);
        } catch (RequestException $exception) {
            return null;
        }

        $carrier = $this->getCarrier($insight);

        return new IdentificationResult(
            carrierName: Arr::get($carrier, 'name'),
            carrierCountry: Arr::get($carrier, 'country'),
            networkType: Arr::get($carrier, 'network_type'),
            callerName: $insight instanceof StandardCnam ? $insight->getCallerName() : null,
            callerType: $insight instanceof StandardCnam ? $insight->getCallerType() : null,
            data: $insight,
        );
    }

    /**
     * Return the current carrier, or original carrier if unavailable.
     */
    protected function getCarrier(Standard $insight): array
    {
        $currentCarrier = $insight->getCurrentCarrier();

        if (count(array_filter($currentCarrier)) === 0) {
            return $insight->getOriginalCarrier();
        }

        return $currentCarrier;
    }
}
