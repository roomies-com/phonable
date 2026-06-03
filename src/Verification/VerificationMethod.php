<?php

namespace Roomies\Phonable\Verification;

enum VerificationMethod
{
    case Automatic;

    case Text;

    case Voice;
}
