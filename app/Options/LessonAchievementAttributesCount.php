<?php

namespace App\Options;

class LessonAchievementAttributesCount
{
    public const FIRST_LESSON_WATCHED = 1;

    public const FIVE_LESSONS_WATCHED = 5;

    public const TEN_LESSONS_WATCHED = 10;

    public const TWENTY_FIVE_LESSONS_WATCHED = 25;

    public const FIFTY_LESSONS_WATCHED = 50;

    public static $choices = [
        1  => 'FIRST_LESSON_WATCHED',
        5  => 'FIVE_LESSONS_WATCHED',
        10 => 'TEN_LESSONS_WATCHED',
        25 => 'TWENTY_FIVE_LESSONS_WATCHED',
        50 => 'FIFTY_LESSONS_WATCHED',
    ];

}
