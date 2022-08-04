<?php

namespace App\Options;

use App\Options\CommentAchievementAttributes;
use App\Options\LessonsAchievementAttributes;

class AchievementTypes
{  
    public const COMMENT  = 'Comment';

    public const LESSON = 'LESSON';


    public static $title = [
        self::COMMENT  => 'Comment',
        self::LESSON   => 'Lesson',
    ];

    public static $type = [
        self::COMMENT  =>  'Comment',
        self::LESSON   =>  'Lesson',
    ];

}