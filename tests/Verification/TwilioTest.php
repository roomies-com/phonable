<?php

namespace Roomies\Phonable\Tests\Verification;

use Illuminate\Support\Facades\Http;
use Roomies\Phonable\Tests\TestCase;
use Roomies\Phonable\Verification\Twilio;
use Roomies\Phonable\Verification\VerificationResult;

class TwilioTest extends TestCase
{
    public function test_send_creates_verification_request(): void
    {
        Http::fake([
            'verify.twilio.com/v2/Services/service_sid/Verifications' => Http::response([
                'sid' => 'abc-123',
            ], 200),
        ]);

        $result = $this->getTwilio()->send('+12125550000');

        $this->assertEquals('abc-123', $result->id);
        $this->assertEquals('+12125550000', $result->phoneNumber);
    }

    public function test_send_creates_verification_request_with_verifiable(): void
    {
        Http::fake([
            'verify.twilio.com/v2/Services/service_sid/Verifications' => Http::response([
                'sid' => 'abc-123',
            ], 200),
        ]);

        $verifiable = new Verifiable;

        $result = $this->getTwilio()->send($verifiable);

        $this->assertEquals('abc-123', $result->id);
        $this->assertEquals($verifiable->getVerifiablePhoneNumber(), $result->phoneNumber);
    }

    public function test_verify_returns_for_valid_code(): void
    {
        Http::fake([
            'verify.twilio.com/v2/Services/service_sid/VerificationCheck' => Http::response([
                'status' => 'approved',
            ], 200),
        ]);

        $result = $this->getTwilio()->verify('request-id', '1234');

        $this->assertEquals(VerificationResult::Successful, $result);
    }

    public function test_verify_returns_for_valid_code_with_verifiable(): void
    {
        Http::fake([
            'verify.twilio.com/v2/Services/service_sid/VerificationCheck' => Http::response([
                'status' => 'approved',
            ], 200),
        ]);

        $verifiable = new Verifiable(sessionId: 'request-id');

        $result = $this->getTwilio()->verify($verifiable, '1234');

        $this->assertEquals(VerificationResult::Successful, $result);
    }

    public function test_verify_returns_for_expired_code(): void
    {
        Http::fake([
            'verify.twilio.com/v2/Services/service_sid/VerificationCheck' => Http::response([
                'status' => 'canceled',
            ], 200),
        ]);

        $result = $this->getTwilio()->verify('request-id', '1234');

        $this->assertEquals(VerificationResult::Expired, $result);
    }

    public function test_verify_returns_for_missing_code(): void
    {
        Http::fake([
            'verify.twilio.com/v2/Services/service_sid/VerificationCheck' => Http::response([
            ], 404),
        ]);

        $result = $this->getTwilio()->verify('request-id', '5678');

        $this->assertEquals(VerificationResult::NotFound, $result);
    }

    public function test_verify_returns_for_invalid_code(): void
    {
        Http::fake([
            'verify.twilio.com/v2/Services/service_sid/VerificationCheck' => Http::response([
                'status' => 'pending',
            ], 200),
        ]);

        $result = $this->getTwilio()->verify('request-id', '5678');

        $this->assertEquals(VerificationResult::Invalid, $result);
    }

    /**
     * Get a Twilio instance with test credentials.
     */
    protected function getTwilio(): Twilio
    {
        return new Twilio('account_sid', 'auth_token', 'service_sid');
    }
}
