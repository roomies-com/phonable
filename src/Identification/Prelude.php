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
        #[SensitiveParameter] protected string $apiKey = '',
        protected string $customerUuid = '',
        protected string $ipAddress = '',
    ) {
        $this->client = Http::baseUrl('https://api.ding.live/v1')
            ->withHeader('x-api-key', $apiKey)
            ->withHeader('customer-uuid', $customerUuid);
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
            carrierName: $response->json('carrier'),
            carrierCountry: $response->json('country_code'),
            networkType: $response->json('line_type'),
            data: $response->json(),
        );
    }
}
