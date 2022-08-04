<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Options\LessonsAchievementAttributes;
use App\Options\CommentAchievementAttributes;

class UserAchievements extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'achievement_type',
        'achievement_name',
        'user_id'
    ];

    /**
     * Get the user that wrote the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

     /**
     * The next achievement after this.
     */
    public function getNextAvailableAchievementAttribute()
    {
        $type = $this->achievement_type;
        $name = $this->achievement_name;

        $achievement = $this->fetchNextAvailableAchievement($type, $name);
        return $achievement;
    }


    public function fetchNextAvailableAchievement($type, $name)
    {
        $type= strtolower($type);
        switch ($type) {
            case 'comment':
                $name = $this->commentAttribute($name);
                break;
            case 'lesson':
                $name = $this->lessonAttribute($name);
                break;
            default:
                $name = null;
        }

        return $name;
    }

    /**
     * fetch next achievement for a comment operations
     */
    protected function commentAttribute($name)
    {
        $attributes = array_flip(CommentAchievementAttributes::$title);
        $current_attribute = $attributes[$name];
        $next_available = null;

        if (isset(CommentAchievementAttributes::$next_available[$current_attribute])) {
            $attrib = CommentAchievementAttributes::$next_available[$current_attribute];
            $next_available = CommentAchievementAttributes::$title[$attrib];
        }

        return $next_available;
    }

    /**
     * fetch next achievement for a lesson watched operation.
     */
    protected function lessonAttribute($name)
    {
        $attributes = array_flip(LessonsAchievementAttributes::$title);
        $current_attribute = $attributes[$name];
        $next_available = null;

        if (isset(LessonsAchievementAttributes::$next_available[$current_attribute])) {
            $attrib = LessonsAchievementAttributes::$next_available[$current_attribute];
            $next_available = LessonsAchievementAttributes::$title[$attrib];
        }

        return $next_available;
    }


}
