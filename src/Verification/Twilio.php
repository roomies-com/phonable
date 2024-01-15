<?php

namespace Roomies\Phonable\Verification;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Roomies\Phonable\Contracts\PhoneVerifiable;
use Roomies\Phonable\Contracts\VerifiesPhoneNumbers;
use SensitiveParameter;

class Twilio implements VerifiesPhoneNumbers
{
    /**
     * The authenticated HTTP client.
     */
    protected PendingRequest $client;

    /**
     * Create a new Twilio instance.
     */
    public function __construct(
        protected string $accountSid = '',
        #[SensitiveParameter] protected string $authToken = '',
        protected string $serviceSid = '',
    ) {
        $this->client = Http::baseUrl("https://verify.twilio.com/v2/Services/{$serviceSid}")
            ->withBasicAuth($accountSid, $authToken);
    }

    /**
     * Send the phone number verification code.
     */
    public function send(PhoneVerifiable $verifiable): VerificationRequest
    {
        $phoneNumber = $verifiable->getVerifiablePhoneNumber();

        $response = $this->client
            ->asForm()
            ->post('/Verifications', [
                'To' => $phoneNumber,
                'Channel' => 'sms',
            ]);

        return new VerificationRequest(
            id: $response->json('sid'),
            phoneNumber: $phoneNumber,
        );
    }

    /**
     * Attempt to complete a phone verification flow.
     */
    public function verify(PhoneVerifiable $verifiable, string $code): VerificationResult
    {
        $phoneNumber = $verifiable->getVerifiablePhoneNumber();

        $response = $this->client
            ->asForm()
            ->post('/VerificationCheck', [
                'VerificationSid' => $verifiable->getVerifiableSession(),
                'Code' => $code,
            ]);

        if ($response->notFound()) {
            return VerificationResult::NotFound;
        }

        return match ($response->json('status')) {
            'approved' => VerificationResult::Successful,
            'pending' => VerificationResult::Invalid,
            'canceled' => VerificationResult::Expired,
        };
    }
}
