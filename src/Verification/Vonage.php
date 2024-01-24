<?php

namespace Roomies\Phonable\Verification;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Roomies\Phonable\Contracts\PhoneVerifiable;
use Roomies\Phonable\Contracts\VerifiesPhoneNumbers;
use Roomies\Phonable\Exceptions\PhoneVerificationAlreadyInProgress;
use SensitiveParameter;

class Vonage implements VerifiesPhoneNumbers
{
    /**
     * The authenticated API request.
     */
    protected PendingRequest $client;

    /**
     * Create a new Vonage client.
     */
    public function __construct(
        string $apiKey = '',
        #[SensitiveParameter] string $apiSecret = ''
    ) {
        $this->client = Http::baseUrl('https://api.nexmo.com')
            ->withBasicAuth($apiKey, $apiSecret);
    }

    /**
     * Send the phone number verification code.
     */
    public function send(string|PhoneVerifiable $verifiable): VerificationRequest
    {
        $phoneNumber = $this->getPhoneNumber($verifiable);

        $response = $this->client
            ->post('/v2/verify', [
                'brand' => config('app.name'),
                'workflow' => [
                    [
                        'channel' => 'sms',
                        'to' => $phoneNumber,
                    ],
                ],
            ]);

        if ($response->status() === 409) {
            throw new PhoneVerificationAlreadyInProgress;
        }

        return new VerificationRequest(
            id: $response->json('request_id'),
            phoneNumber: $phoneNumber,
        );
    }

    /**
     * Attempt to complete a phone verification flow.
     */
    public function verify(string|PhoneVerifiable $verifiable, string $code): VerificationResult
    {
        $response = $this->client
            ->post("/v2/verify/{$verifiable->getVerifiableSession()}", [
                'code' => $code,
            ]);

        if ($response->status() === 404) {
            return VerificationResult::NotFound;
        }

        if ($response->status() === 410) {
            return VerificationResult::Expired;
        }

        if ($response->status() !== 200) {
            return VerificationResult::Invalid;
        }

        return VerificationResult::Successful;
    }

    /**
     * Get the phone number off a PhoneVerifiable instance if provided.
     */
    protected function getPhoneNumber(string|PhoneVerifiable $verifiable): ?string
    {
        return $verifiable instanceof PhoneVerifiable
            ? $verifiable->getVerifiablePhoneNumber()
            : $verifiable;
    }
}
