<?php

namespace Roomies\Phonable\Facades;

use Illuminate\Support\Facades\Facade;
use Roomies\Phonable\Identification\IdentificationFake;

/**
 * @see \Roomies\Phonable\Identification\Manager
 */
class Identification extends Facade
{
    /**
     * Replace the bound instance with a fake.
     */
    public static function fake($identificationsToFake = []): IdentificationFake
    {
        $identificationsToFake = Arr::wrap($identificationsToFake);

        return tap(new IdentificationFake($identificationsToFake), function ($fake) {
            static::swap($fake);
        });
    }

    protected static function getFacadeAccessor()
    {
        return 'phone-identification';
    }
}
