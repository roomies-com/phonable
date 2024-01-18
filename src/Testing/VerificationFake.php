<?php

namespace Roomies\Phonable\Testing;

use Illuminate\Support\Str;
use Illuminate\Support\Testing\Fakes\Fake;
use PHPUnit\Framework\Assert as PHPUnit;
use Roomies\Phonable\Contracts\PhoneVerifiable;
use Roomies\Phonable\Contracts\VerifiesPhoneNumbers;
use Roomies\Phonable\Verification\VerificationRequest;
use Roomies\Phonable\Verification\VerificationResult;

class VerificationFake implements Fake, VerifiesPhoneNumbers
{
    /**
     * The phone verification requests that have been sent.
     */
    protected array $requests = [];

    /**
     * The phone verification results that have been received.
     */
    protected array $responses = [];

    /**
     * Create a new fake instance.
     */
    public function __construct(protected array $verificationsToFake = [])
    {
        foreach ($verificationsToFake as $key => $result) {
            $this->responses[$key] = $result;
        }
    }

    /**
     * Do not change the driver if requested.
     */
    public function driver(string $driver)
    {
        return $this;
    }

    /**
     * Send the phone number verification code.
     */
    public function send(PhoneVerifiable $verifiable): VerificationRequest
    {
        $request = new VerificationRequest(
            id: Str::random(),
            phoneNumber: $verifiable->getVerifiablePhoneNumber(),
        );

        return $this->requests[$verifiable->getVerifiablePhoneNumber()] = $request;
    }

    /**
     * Attempt to verify the phone number code.
     */
    public function verify(PhoneVerifiable $verifiable, string $code): VerificationResult
    {
        return $this->responses[$verifiable->getVerifiablePhoneNumber()]
            ?? VerificationResult::NotFound;
    }

    /**
     * Assert if a verification was sent.
     *
     * @throws \Exception
     */
    public function assertSentTo(PhoneVerifiable $verifiable): void
    {
        PHPUnit::assertTrue(
            array_key_exists($verifiable->getVerifiablePhoneNumber(), $this->requests),
            'The expected verification was not sent.'
        );
    }

    /**
     * Assert if a verification was not sent.
     *
     * @throws \Exception
     */
    public function assertNotSentTo(PhoneVerifiable $verifiable): void
    {
        PHPUnit::assertFalse(
            array_key_exists($verifiable->getVerifiablePhoneNumber(), $this->requests),
            'The unexpected verification was sent.'
        );
    }

    /**
     * Assert that no notifications were sent.
     *
     * @throws \Exception
     */
    public function assertNothingSent(): void
    {
        PHPUnit::assertEmpty($this->requests, 'Verifications were sent unexpectedly.');
    }
}
