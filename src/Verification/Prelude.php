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
    public function send(string|PhoneVerifiable $verifiable, VerificationMethod $method = VerificationMethod::Automatic): VerificationRequest
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
                'options' => [
                    'method' => $this->method($method),
                ],
            ]);

        return new VerificationRequest(
            id: $response->json('id'),
            phoneNumber: $phoneNumber,
            status: $this->status($response),
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

    /**
     * Map the verification method to the Prelude option.
     */
    protected function method(VerificationMethod $method): string
    {
        return match ($method) {
            VerificationMethod::Voice => 'voice',
            VerificationMethod::Text => 'message',
            VerificationMethod::Automatic => 'auto',
        };
    }

    /**
     * Determine the status of the verification request.
     */
    protected function status($response): VerificationRequestStatus
    {
        if (in_array($response->json('status'), ['success', 'retry'])) {
            return VerificationRequestStatus::Successful;
        }

        if ($response->json('status') === 'shadow_blocked'
            || in_array($response->json('reason'), ['in_block_list', 'suspicious'])) {
            return VerificationRequestStatus::Blocked;
        }

        return VerificationRequestStatus::Failed;
    }
}
