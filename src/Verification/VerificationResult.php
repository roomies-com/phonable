<?php

namespace Roomies\Phonable\Verification;

enum VerificationResult
{
    /**
     * The verification request was successful.
     */
    case Successful;

    /**
     * The verification request could not be found.
     */
    case NotFound;

    /**
     * The verification request has expired.
     */
    case Expired;

    /**
     * The verifaction token provided was invalid.
     */
    case Invalid;
}
