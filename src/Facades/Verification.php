<?php

namespace Roomies\Phonable\Facades;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Facade;
use Roomies\Phonable\Contracts\PhoneVerifiable;
use Roomies\Phonable\Testing\VerificationFake;
use Roomies\Phonable\Verification\Manager;
use Roomies\Phonable\Verification\VerificationMethod;
use Roomies\Phonable\Verification\VerificationRequest;
use Roomies\Phonable\Verification\VerificationResult;

/**
 * @method static VerificationRequest send(string|PhoneVerifiable $verifiable, VerificationMethod $method = VerificationMethod::Automatic, array $options = [])
 * @method static VerificationResult verify(string|PhoneVerifiable $verifiable, string $code)
 *
 * @see Manager
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
