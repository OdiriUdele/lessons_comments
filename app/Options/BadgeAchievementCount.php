<?php

namespace App\Options;

class BadgeAchievementCount
{
    public const BEGINNER = 0;

    public const INTERMEDIATE = 4;

    public const ADVANCED = 8;

    public const MASTER = 10;

    public static $choices = [
        0  => 'BEGINNER',
        4  => 'INTERMEDIATE',
        8  => 'ADVANCED',
        10 => 'MASTER',
    ];
}
