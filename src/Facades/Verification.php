<?php

namespace Roomies\Phonable\Facades;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Facade;
use Roomies\Phonable\Testing\VerificationFake;

/**
 * @see \Roomies\Phonable\Verification\Manager
 */
class Verification extends Facade
{
    /**
     * Replace the bound instance with a fake.
     */
    public static function fake($verificationsToFake = []): VerificationFake
    {
        $verificationsToFake = Arr::wrap($verificationsToFake);

        return tap(new VerificationFake($verificationsToFake), function ($fake) {
            static::swap($fake);
        });
    }

    protected static function getFacadeAccessor()
    {
        return 'phone-verification';
    }
}
