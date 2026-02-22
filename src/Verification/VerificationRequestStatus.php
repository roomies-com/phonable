<?php

namespace Roomies\Phonable\Verification;

enum VerificationRequestStatus
{
    case Successful;

    case Failed;

    case Blocked;
}
