<?php

namespace Roomies\Phonable\Tests\Testing;

use PHPUnit\Framework\ExpectationFailedException;
use Roomies\Phonable\Testing\VerificationFake;
use Roomies\Phonable\Tests\TestCase;
use Roomies\Phonable\Tests\Verification\Verifiable;
use Roomies\Phonable\Verification\VerificationResult;

class VerificationFakeTest extends TestCase
{
    public Verifiable $verifiable;

    public VerificationFake $fake;

    public function setUp(): void
    {
        $this->verifiable = new Verifiable;
    }

    public function test_assert_sent_to()
    {
        $fake = new VerificationFake;

        try {
            $fake->assertSentTo($this->verifiable);
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString('The expected verification was not sent', $e->getMessage());
        }

        $fake->send($this->verifiable);

        $fake->assertSentTo($this->verifiable);
    }

    public function test_assert_not_sent_to()
    {
        $fake = new VerificationFake;

        $fake->assertNotSentTo($this->verifiable);

        $fake->send($this->verifiable);

        try {
            $fake->assertNotSentTo($this->verifiable);
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString('The unexpected verification was sent', $e->getMessage());
        }
    }

    public function test_assert_nothing_sent()
    {
        $fake = new VerificationFake;

        $fake->assertNothingSent();

        $fake->send($this->verifiable);

        try {
            $fake->assertNothingSent();
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString('Verifications were sent unexpectedly.', $e->getMessage());
        }
    }

    public function test_faking_successful_verification()
    {
        $fake = new VerificationFake([
            '+12125550000' => VerificationResult::Successful,
        ]);

        $result = $fake->verify($this->verifiable, 1234);

        $this->assertEquals(VerificationResult::Successful, $result);
    }

    public function test_faking_expired_verification()
    {
        $fake = new VerificationFake([
            '+12125550000' => VerificationResult::Expired,
        ]);

        $result = $fake->verify($this->verifiable, 1234);

        $this->assertEquals(VerificationResult::Expired, $result);
    }

    public function test_faking_verification_without_result()
    {
        $fake = new VerificationFake;

        $result = $fake->verify($this->verifiable, 1234);

        $this->assertEquals(VerificationResult::NotFound, $result);
    }
}
