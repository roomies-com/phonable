<?php

namespace Roomies\Phonable\Tests\Verification;

use Illuminate\Support\Facades\Http;
use Roomies\Phonable\Tests\TestCase;
use Roomies\Phonable\Verification\Prelude;
use Roomies\Phonable\Verification\VerificationMethod;
use Roomies\Phonable\Verification\VerificationRequestStatus;
use Roomies\Phonable\Verification\VerificationResult;

class PreludeTest extends TestCase
{
    public function test_send_creates_verification_request(): void
    {
        Http::fake([
            'api.prelude.dev/v2/verification' => Http::response([
                'id' => 'abc-123',
                'status' => 'success',
            ], 200),
        ]);

        $result = app(Prelude::class)->send('+12125550000');

        $this->assertEquals('abc-123', $result->id);
        $this->assertEquals('+12125550000', $result->phoneNumber);
        $this->assertEquals(VerificationRequestStatus::Successful, $result->status);
    }

    public function test_send_creates_verification_request_with_verifiable(): void
    {
        Http::fake([
            'api.prelude.dev/v2/verification' => Http::response([
                'id' => 'abc-123',
                'status' => 'success',
            ], 200),
        ]);

        $verifiable = new Verifiable;

        $result = app(Prelude::class)->send($verifiable);

        $this->assertEquals('abc-123', $result->id);
        $this->assertEquals($verifiable->getVerifiablePhoneNumber(), $result->phoneNumber);
        $this->assertEquals(VerificationRequestStatus::Successful, $result->status);
    }

    public function test_send_creates_verification_request_with_blocked_verifiable(): void
    {
        Http::fake([
            'api.prelude.dev/v2/verification' => Http::response([
                'id' => 'abc-123',
                'status' => 'blocked',
                'reason' => 'suspicious',
            ], 200),
        ]);

        $verifiable = new Verifiable;

        $result = app(Prelude::class)->send($verifiable);

        $this->assertEquals('abc-123', $result->id);
        $this->assertEquals($verifiable->getVerifiablePhoneNumber(), $result->phoneNumber);
        $this->assertEquals(VerificationRequestStatus::Blocked, $result->status);
    }

    public function test_send_returns_failed_for_undeliverable_number(): void
    {
        Http::fake([
            'api.prelude.dev/v2/verification' => Http::response([
                'id' => 'abc-123',
                'status' => 'blocked',
                'reason' => 'invalid_phone_number',
            ], 200),
        ]);

        $result = app(Prelude::class)->send('+12125550000');

        $this->assertEquals(VerificationRequestStatus::Failed, $result->status);
    }

    public function test_send_returns_blocked_when_shadow_blocked(): void
    {
        Http::fake([
            'api.prelude.dev/v2/verification' => Http::response([
                'id' => 'abc-123',
                'status' => 'shadow_blocked',
            ], 200),
        ]);

        $result = app(Prelude::class)->send('+12125550000');

        $this->assertEquals(VerificationRequestStatus::Blocked, $result->status);
    }

    public function test_send_treats_a_reused_verification_as_successful(): void
    {
        Http::fake([
            'api.prelude.dev/v2/verification' => Http::response([
                'id' => 'abc-123',
                'status' => 'retry',
            ], 200),
        ]);

        $result = app(Prelude::class)->send('+12125550000');

        $this->assertEquals(VerificationRequestStatus::Successful, $result->status);
    }

    public function test_send_returns_failed_when_the_request_errors(): void
    {
        Http::fake([
            'api.prelude.dev/v2/verification' => Http::response([
                'message' => 'Something went wrong.',
            ], 500),
        ]);

        $result = app(Prelude::class)->send('+12125550000');

        $this->assertEquals(VerificationRequestStatus::Failed, $result->status);
    }

    public function test_send_defaults_to_automatic_method(): void
    {
        Http::fake([
            'api.prelude.dev/v2/verification' => Http::response([
                'id' => 'abc-123',
                'status' => 'success',
            ], 200),
        ]);

        app(Prelude::class)->send('+12125550000');

        Http::assertSent(function ($request) {
            return $request['options']['method'] === 'auto';
        });
    }

    public function test_send_requests_voice_method(): void
    {
        Http::fake([
            'api.prelude.dev/v2/verification' => Http::response([
                'id' => 'abc-123',
                'status' => 'success',
            ], 200),
        ]);

        app(Prelude::class)->send('+12125550000', method: VerificationMethod::Voice);

        Http::assertSent(function ($request) {
            return $request['options']['method'] === 'voice';
        });
    }

    public function test_verify_returns_for_valid_code(): void
    {
        Http::fake([
            'api.prelude.dev/v2/verification/check' => Http::response([
                'status' => 'success',
            ], 200),
        ]);

        $result = app(Prelude::class)->verify('+12125550000', '1234');

        $this->assertEquals(VerificationResult::Successful, $result);
    }

    public function test_verify_returns_for_valid_code_with_verifiable(): void
    {
        Http::fake([
            'api.prelude.dev/v2/verification/check' => Http::response([
                'status' => 'success',
            ], 200),
        ]);

        $result = app(Prelude::class)->verify(new Verifiable, '1234');

        $this->assertEquals(VerificationResult::Successful, $result);
    }

    public function test_verify_returns_for_already_valid_code(): void
    {
        Http::fake([
            'api.prelude.dev/v2/verification/check' => Http::response([
                'status' => 'success',
            ], 200),
        ]);

        $result = app(Prelude::class)->verify('+12125550000', '1234');

        $this->assertEquals(VerificationResult::Successful, $result);
    }

    public function test_verify_returns_for_expired_code(): void
    {
        Http::fake([
            'api.prelude.dev/v2/verification/check' => Http::response([
                'status' => 'expired_or_not_found',
            ], 200),
        ]);

        $result = app(Prelude::class)->verify('+12125550000', '1234');

        $this->assertEquals(VerificationResult::Expired, $result);
    }

    public function test_verify_returns_for_invalid_code(): void
    {
        Http::fake([
            'api.prelude.dev/v2/verification/check' => Http::response([
                'status' => 'failure',
            ], 200),
        ]);

        $result = app(Prelude::class)->verify('+12125550000', '5678');

        $this->assertEquals(VerificationResult::Invalid, $result);
    }
}
