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
        #[SensitiveParameter] protected string $key = '',
        protected string $ipAddress = '',
        protected string $userAgent = '',
    ) {
        $this->client = Http::baseUrl('https://api.prelude.dev/v2')
            ->withHeader('Accept', 'application/json')
            ->withToken($key);
    }

    /**
     * Send the phone number verification code.
     */
    public function send(string|PhoneVerifiable $verifiable): VerificationRequest
    {
        $phoneNumber = $this->getPhoneNumber($verifiable);

        $response = $this->client
            ->post('/verification', [
                'target' => [
                    'type' => 'phone_number',
                    'value' => $phoneNumber,
                ],
                'signals' => [
                    'ip' => $this->ipAddress,
                    'user_agent' => $this->userAgent,
                    'device_platform' => 'web',
                ],
            ]);

        $status = $response->json('status') === 'blocked'
            ? VerificationRequestStatus::Blocked
            : VerificationRequestStatus::Successful;

        return new VerificationRequest(
            id: $response->json('id'),
            phoneNumber: $phoneNumber,
            status: $status,
            raw: $response,
        );
    }

    /**
     * Attempt to complete a phone verification flow.
     */
    public function verify(string|PhoneVerifiable $verifiable, string $code): VerificationResult
    {
        $phoneNumber = $this->getPhoneNumber($verifiable);

        $response = $this->client
            ->post('/verification/check', [
                'target' => [
                    'type' => 'phone_number',
                    'value' => $phoneNumber,
                ],
                'code' => $code,
            ]);

        return match ($response->json('status')) {
            'success' => VerificationResult::Successful,
            'failure' => VerificationResult::Invalid,
            'expired_or_not_found' => VerificationResult::Expired,
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
}
