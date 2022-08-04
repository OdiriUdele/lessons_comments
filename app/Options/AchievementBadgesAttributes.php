<?php

namespace App\Options;

class AchievementBadgesAttributes
{
    public const BEGINNER = 'BEGINNER';

    public const INTERMEDIATE = 'INTERMEDIATE';

    public const ADVANCED = 'ADVANCED';

    public const MASTER = 'MASTER';

    public static $count = [
        self::BEGINNER     => 0,
        self::INTERMEDIATE => 4,
        self::ADVANCED     => 8,
        self::MASTER       => 10,
    ];

    public static $title = [
        self::BEGINNER     => 'Beginner',
        self::INTERMEDIATE => 'Intermediate',
        self::ADVANCED     => 'Advanced',
        self::MASTER       => 'Master',
    ];

    public static $next_available = [
        self::BEGINNER     => self::INTERMEDIATE,
        self::INTERMEDIATE => self::ADVANCED,
        self::ADVANCED     => self::MASTER
    ];
}
