<?php

namespace Roomies\Phonable\Tests\Verification;

use Illuminate\Support\Facades\Http;
use Roomies\Phonable\Tests\TestCase;
use Roomies\Phonable\Verification\Ding;
use Roomies\Phonable\Verification\VerificationResult;

class DingTest extends TestCase
{
    public function test_send_creates_verification_request(): void
    {
        Http::fake([
            'api.ding.live/v1/authentication' => Http::response([
                'authentication_uuid' => 'abc-123',
            ], 200),
        ]);

        $result = app(Ding::class)->send('+12125550000');

        $this->assertEquals('abc-123', $result->id);
        $this->assertEquals('+12125550000', $result->phoneNumber);
    }

    public function test_send_creates_verification_request_with_verifiable(): void
    {
        Http::fake([
            'api.ding.live/v1/authentication' => Http::response([
                'authentication_uuid' => 'abc-123',
            ], 200),
        ]);

        $verifiable = new Verifiable;

        $result = app(Ding::class)->send($verifiable);

        $this->assertEquals('abc-123', $result->id);
        $this->assertEquals($verifiable->getVerifiablePhoneNumber(), $result->phoneNumber);
    }

    public function test_verify_returns_for_valid_code(): void
    {
        Http::fake([
            'api.ding.live/v1/check' => Http::response([
                'status' => 'valid',
            ], 200),
        ]);

        $result = app(Ding::class)->verify('request-id', '1234');

        $this->assertEquals(VerificationResult::Successful, $result);
    }

    public function test_verify_returns_for_valid_code_with_verifiable(): void
    {
        Http::fake([
            'api.ding.live/v1/check' => Http::response([
                'status' => 'valid',
            ], 200),
        ]);

        $verifiable = new Verifiable(sessionId: 'request-id');

        $result = app(Ding::class)->verify($verifiable, '1234');

        $this->assertEquals(VerificationResult::Successful, $result);
    }

    public function test_verify_returns_for_already_valid_code(): void
    {
        Http::fake([
            'api.ding.live/v1/check' => Http::response([
                'status' => 'already_validated',
            ], 200),
        ]);

        $result = app(Ding::class)->verify('request-id', '1234');

        $this->assertEquals(VerificationResult::Successful, $result);
    }

    public function test_verify_returns_for_expired_code(): void
    {
        Http::fake([
            'api.ding.live/v1/check' => Http::response([
                'status' => 'expired_auth',
            ], 200),
        ]);

        $result = app(Ding::class)->verify('request-id', '1234');

        $this->assertEquals(VerificationResult::Expired, $result);
    }

    public function test_verify_returns_for_missing_code(): void
    {
        Http::fake([
            'api.ding.live/v1/check' => Http::response([
                'status' => 'without_attempt',
            ], 200),
        ]);

        $result = app(Ding::class)->verify('request-id', '5678');

        $this->assertEquals(VerificationResult::NotFound, $result);
    }

    public function test_verify_returns_for_invalid_code(): void
    {
        Http::fake([
            'api.ding.live/v1/check' => Http::response([
                'status' => 'invalid',
            ], 200),
        ]);

        $result = app(Ding::class)->verify('request-id', '5678');

        $this->assertEquals(VerificationResult::Invalid, $result);
    }
}
