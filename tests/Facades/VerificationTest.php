<?php

namespace Roomies\Phonable\Tests\Facades;

use Roomies\Phonable\Facades\Verification;
use Roomies\Phonable\Tests\TestCase;
use Roomies\Phonable\Verification\VerificationFake;

class VerificationTest extends TestCase
{
    public function test_it_returns_instance_of_fake()
    {
        $result = Verification::fake();

        $this->assertInstanceOf(VerificationFake::class, $result);
    }
}
