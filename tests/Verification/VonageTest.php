<?php

namespace Roomies\Phonable\Tests\Verification;

use Illuminate\Support\Facades\Http;
use Roomies\Phonable\Tests\TestCase;
use Roomies\Phonable\Verification\VerificationResult;
use Roomies\Phonable\Verification\Vonage;

class VonageTest extends TestCase
{
    public function test_send_creates_verification_request()
    {
        Http::fake([
            'api.nexmo.com/v2/verify' => Http::response([
                'request_id' => 'abc-123',
            ], 200),
        ]);

        $verifiable = new Verifiable;

        $result = app(Vonage::class)->send($verifiable);

        $this->assertEquals('abc-123', $result->id);
        $this->assertEquals($verifiable->getVerifiablePhoneNumber(), $result->phoneNumber);
    }

    public function test_verify_returns_for_valid_code()
    {
        Http::fake([
            'api.nexmo.com/v2/verify/request-id' => Http::response([], 200),
        ]);

        $verifiable = new Verifiable(sessionId: 'request-id');

        $result = app(Vonage::class)->verify($verifiable, '1234');

        $this->assertEquals(VerificationResult::Successful, $result);
    }

    public function test_verify_returns_for_expired_valid_code()
    {
        Http::fake([
            'api.nexmo.com/v2/verify/request-id' => Http::response([], 410),
        ]);

        $verifiable = new Verifiable(sessionId: 'request-id');

        $result = app(Vonage::class)->verify($verifiable, '1234');

        $this->assertEquals(VerificationResult::Expired, $result);
    }

    public function test_verify_returns_for_missing_code()
    {
        Http::fake([
            'api.nexmo.com/v2/verify/request-id' => Http::response([], 404),
        ]);

        $verifiable = new Verifiable(sessionId: 'request-id');

        $result = app(Vonage::class)->verify($verifiable, '5678');

        $this->assertEquals(VerificationResult::NotFound, $result);
    }

    public function test_verify_returns_for_invalid_code()
    {
        Http::fake([
            'api.nexmo.com/v2/verify/request-id' => Http::response([], 400),
        ]);

        $verifiable = new Verifiable(sessionId: 'request-id');

        $result = app(Vonage::class)->verify($verifiable, '5678');

        $this->assertEquals(VerificationResult::Invalid, $result);
    }
}
