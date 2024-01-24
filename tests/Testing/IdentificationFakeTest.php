<?php

namespace Roomies\Phonable\Tests\Testing;

use Roomies\Phonable\Identification\IdentificationResult;
use Roomies\Phonable\Testing\IdentificationFake;
use Roomies\Phonable\Tests\Identification\Identifiable;
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

        $result = $fake->get($this->identifiable);

        $this->assertInstanceOf(IdentificationResult::class, $result);
    }

    public function test_faking_verification_without_result()
    {
        $fake = new IdentificationFake();

        $result = $fake->get($this->identifiable);

        $this->assertNull($result);
    }
}
