<?php

namespace App\Enums;

enum PersonalityTrait: string
{
    case Openness          = 'openness';
    case Conscientiousness = 'conscientiousness';
    case Extraversion      = 'extraversion';
    case Agreeableness     = 'agreeableness';
    case Neuroticism       = 'neuroticism';
}
