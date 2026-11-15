<?php

namespace Roomies\Phonable\Facades;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Facade;
use Roomies\Phonable\Contracts\PhoneIdentifiable;
use Roomies\Phonable\Identification\IdentificationResult;
use Roomies\Phonable\Identification\Manager;
use Roomies\Phonable\Testing\IdentificationFake;

/**
 * @method static IdentificationResult|null get(string|PhoneIdentifiable $identifiable)
 *
 * @see Manager
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
