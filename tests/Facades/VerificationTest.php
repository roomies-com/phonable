<?php

namespace Roomies\Phonable\Tests\Facades;

use Roomies\Phonable\Facades\Verification;
use Roomies\Phonable\Testing\VerificationFake;
use Roomies\Phonable\Tests\TestCase;

class VerificationTest extends TestCase
{
    public function test_it_returns_instance_of_fake(): void
    {
        $result = Verification::fake();

        $this->assertInstanceOf(VerificationFake::class, $result);
    }
}
