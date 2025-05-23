<?php

namespace Roomies\Phonable\Identification;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Roomies\Phonable\Contracts\IdentifiesPhoneNumbers;
use Roomies\Phonable\Contracts\PhoneIdentifiable;
use SensitiveParameter;

class Prelude implements IdentifiesPhoneNumbers
{
    /**
     * The authenticated HTTP client.
     */
    protected PendingRequest $client;

    /**
     * Create a new Prelude instance.
     */
    public function __construct(
        #[SensitiveParameter] protected string $key = '',
        protected string $ipAddress = '',
    ) {
        $this->client = Http::baseUrl('https://api.prelude.dev/v2')
            ->withHeader('Accept', 'application/json')
            ->withToken($key);
    }

    /**
     * Fetch the identification for the given phone number.
     */
    public function get(string|PhoneIdentifiable $identifiable): ?IdentificationResult
    {
        $phoneNumber = $identifiable instanceof PhoneIdentifiable
            ? $identifiable->getIdentifiablePhoneNumber()
            : $identifiable;

        $response = $this->client->get("/lookup/{$phoneNumber}");

        if ($response->failed()) {
            return null;
        }

        return new IdentificationResult(
            carrierName: $response->json('network_info.carrier_name'),
            carrierCountry: $response->json('country_code'),
            networkType: $response->json('line_type'),
            data: $response->json(),
        );
    }
}
