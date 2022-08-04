<?php

namespace App\Options;

class CommentAchievementAttributesCount
{  
    public const FIRST_COMMENT_WRITTEN = 1;

    public const THREE_COMMENTS_WRITTEN = 3;

    public const FIVE_COMMENTS_WRITTEN = 5;

    public const TEN_COMMENTS_WRITTEN = 10;

    public const TWENTY_COMMENTS_WRITTEN = 20;

    public static $choices = [
        1  => 'FIRST_COMMENT_WRITTEN',
        3  => 'THREE_COMMENTS_WRITTEN',
        5  => 'FIVE_COMMENTS_WRITTEN',
        10 => 'TEN_COMMENTS_WRITTEN',
        20 => 'TWENTY_COMMENTS_WRITTEN',
    ];
}
