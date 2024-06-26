<?php

namespace Roomies\Phonable\Verification;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Roomies\Phonable\Contracts\PhoneVerifiable;
use Roomies\Phonable\Contracts\VerifiesPhoneNumbers;
use SensitiveParameter;

class Prelude implements VerifiesPhoneNumbers
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
            ->withHeader('x-api-key', $apiKey);
    }

    /**
     * Send the phone number verification code.
     */
    public function send(string|PhoneVerifiable $verifiable): VerificationRequest
    {
        $phoneNumber = $this->getPhoneNumber($verifiable);

        $response = $this->client
            ->post('/authentication', [
                'customer_uuid' => $this->customerUuid,
                'phone_number' => $phoneNumber,
                'ip' => $this->ipAddress,
                'device_type' => 'WEB',
            ]);

        return new VerificationRequest(
            id: $response->json('authentication_uuid'),
            phoneNumber: $phoneNumber,
        );
    }

    /**
     * Attempt to complete a phone verification flow.
     */
    public function verify(string|PhoneVerifiable $verifiable, string $code): VerificationResult
    {
        $session = $this->getVerifiableSession($verifiable);

        $response = $this->client
            ->post('/check', [
                'customer_uuid' => $this->customerUuid,
                'authentication_uuid' => $session,
                'check_code' => $code,
            ]);

        return match ($response->json('status')) {
            'valid' => VerificationResult::Successful,
            'already_validated' => VerificationResult::Successful,
            'invalid' => VerificationResult::Invalid,
            'without_attempt' => VerificationResult::NotFound,
            'expired_auth' => VerificationResult::Expired,
            default => VerificationResult::Invalid,
        };
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

    /**
     * Get the phone verification session off a PhoneVerifiable instance if provided.
     */
    protected function getVerifiableSession(string|PhoneVerifiable $verifiable): ?string
    {
        return $verifiable instanceof PhoneVerifiable
            ? $verifiable->getVerifiableSession()
            : $verifiable;
    }
}
