<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class LoginType extends Enum
{
    const EMAIL = 1;
    const FACEBOOK = 2;
    const TWITTER = 3;
    const LINKEDIN = 4;
    const GOOGLE = 5;
    const GITHUB = 6;
    const GITLAB = 7;
    const BITBUCKET = 8;
}
