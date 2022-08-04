<?php

namespace App\Options;

use App\Options\AchievementTypes;

class LessonsAchievementAttributes
{
    public const ACHIEVEMENT_TYPE  = AchievementTypes::LESSON;

    public const FIRST_LESSON_WATCHED = 'FIRST_LESSON_WATCHED';

    public const FIVE_LESSONS_WATCHED = 'FIVE_LESSONS_WATCHED';

    public const TEN_LESSONS_WATCHED = 'TEN_LESSONS_WATCHED';

    public const TWENTY_FIVE_LESSONS_WATCHED = 'TWENTY_FIVE_LESSONS_WATCHED';

    public const FIFTY_LESSONS_WATCHED = 'FIFTY_LESSONS_WATCHED';

    public static $count = [
        self::FIRST_LESSON_WATCHED        => 1,
        self::FIVE_LESSONS_WATCHED        => 5,
        self::TEN_LESSONS_WATCHED         => 10,
        self::TWENTY_FIVE_LESSONS_WATCHED => 25,
        self::FIFTY_LESSONS_WATCHED       => 50,
    ];

    public static $title = [
        self::FIRST_LESSON_WATCHED         => 'First Lesson Watched',
        self::FIVE_LESSONS_WATCHED         => '5 Lessons Watched',
        self::TEN_LESSONS_WATCHED          => '10 Lessons Watched',
        self::TWENTY_FIVE_LESSONS_WATCHED  => '25 Lessons Watched',
        self::FIFTY_LESSONS_WATCHED        => '50 Lessons Watched',
    ];

    public static $next_available = [
        self::FIRST_LESSON_WATCHED        => self::FIVE_LESSONS_WATCHED,
        self::FIVE_LESSONS_WATCHED        => self::TEN_LESSONS_WATCHED,
        self::TEN_LESSONS_WATCHED         => self::TWENTY_FIVE_LESSONS_WATCHED,
        self::TWENTY_FIVE_LESSONS_WATCHED => self::FIFTY_LESSONS_WATCHED
    ];

}
