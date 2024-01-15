<?php

namespace Roomies\Phonable\Tests\Identification;

use Roomies\Phonable\Identification\IdentificationFake;
use Roomies\Phonable\Identification\IdentificationResult;
use Roomies\Phonable\Tests\TestCase;

class IdentificationFakeTest extends TestCase
{
    public Identifiable $identifiable;

    public IdentificationFake $fake;

    public function setUp(): void
    {
        $this->identifiable = new Identifiable;
    }

    public function test_faking_successful_verification()
    {
        $fake = new IdentificationFake([
            '+12125550000' => new IdentificationResult,
        ]);

        $result = $fake->handle($this->identifiable);

        $this->assertInstanceOf(IdentificationResult::class, $result);
    }

    public function test_faking_verification_without_result()
    {
        $fake = new IdentificationFake();

        $result = $fake->handle($this->identifiable);

        $this->assertNull($result);
    }
}
