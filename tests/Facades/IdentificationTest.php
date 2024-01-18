<?php

namespace Roomies\Phonable\Tests\Facades;

use Roomies\Phonable\Facades\Identification;
use Roomies\Phonable\Testing\IdentificationFake;
use Roomies\Phonable\Tests\TestCase;

class IdentificationTest extends TestCase
{
    public function test_it_returns_instance_of_fake()
    {
        $result = Identification::fake();

        $this->assertInstanceOf(IdentificationFake::class, $result);
    }
}
