<?php

namespace Roomies\Phonable\Tests\Verification;

use Illuminate\Support\Facades\Http;
use Roomies\Phonable\Tests\TestCase;
use Roomies\Phonable\Verification\VerificationMethod;
use Roomies\Phonable\Verification\VerificationRequestStatus;
use Roomies\Phonable\Verification\VerificationResult;
use Roomies\Phonable\Verification\Vonage;

class VonageTest extends TestCase
{
    public function test_send_creates_verification_request(): void
    {
        Http::fake([
            'api.nexmo.com/v2/verify' => Http::response([
                'request_id' => 'abc-123',
            ], 200),
        ]);

        $result = app(Vonage::class)->send('+12125550000');

        $this->assertEquals('abc-123', $result->id);
        $this->assertEquals('+12125550000', $result->phoneNumber);
        $this->assertEquals(VerificationRequestStatus::Successful, $result->status);
    }

    public function test_send_creates_verification_request_with_verifiable(): void
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
        $this->assertEquals(VerificationRequestStatus::Successful, $result->status);
    }

    public function test_send_defaults_to_sms_channel(): void
    {
        Http::fake([
            'api.nexmo.com/v2/verify' => Http::response([
                'request_id' => 'abc-123',
            ], 200),
        ]);

        app(Vonage::class)->send('+12125550000');

        Http::assertSent(fn ($request) => $request['workflow'][0]['channel'] === 'sms');
    }

    public function test_send_requests_voice_channel(): void
    {
        Http::fake([
            'api.nexmo.com/v2/verify' => Http::response([
                'request_id' => 'abc-123',
            ], 200),
        ]);

        app(Vonage::class)->send('+12125550000', method: VerificationMethod::Voice);

        Http::assertSent(fn ($request) => $request['workflow'][0]['channel'] === 'voice');
    }

    public function test_verify_returns_for_valid_code(): void
    {
        Http::fake([
            'api.nexmo.com/v2/verify/request-id' => Http::response([], 200),
        ]);

        $result = app(Vonage::class)->verify('request-id', '1234');

        $this->assertEquals(VerificationResult::Successful, $result);
    }

    public function test_verify_returns_for_valid_code_with_verifiable(): void
    {
        Http::fake([
            'api.nexmo.com/v2/verify/request-id' => Http::response([], 200),
        ]);

        $verifiable = new Verifiable(sessionId: 'request-id');

        $result = app(Vonage::class)->verify($verifiable, '1234');

        $this->assertEquals(VerificationResult::Successful, $result);
    }

    public function test_verify_returns_for_expired_valid_code(): void
    {
        Http::fake([
            'api.nexmo.com/v2/verify/request-id' => Http::response([], 410),
        ]);

        $result = app(Vonage::class)->verify('request-id', '1234');

        $this->assertEquals(VerificationResult::Expired, $result);
    }

    public function test_verify_returns_for_missing_code(): void
    {
        Http::fake([
            'api.nexmo.com/v2/verify/request-id' => Http::response([], 404),
        ]);

        $result = app(Vonage::class)->verify('request-id', '5678');

        $this->assertEquals(VerificationResult::NotFound, $result);
    }

    public function test_verify_returns_for_invalid_code(): void
    {
        Http::fake([
            'api.nexmo.com/v2/verify/request-id' => Http::response([], 400),
        ]);

        $result = app(Vonage::class)->verify('request-id', '5678');

        $this->assertEquals(VerificationResult::Invalid, $result);
    }
}
