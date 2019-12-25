<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class LoginType extends Enum
{
    const EMAIL = "EMAIL";
    const FACEBOOK = "FACEBOOK";
    const TWITTER = "TWITTER";
    const LINKEDIN = "LINKEDIN";
    const GOOGLE = "GOOGLE";
    const GITHUB = "GITHUB";
    const GITLAB = "GITLAB";
    const BITBUCKET = "BITBUCKET";
}
