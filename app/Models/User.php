<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\UserAchievements;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Options\AchievementBadgesAttributes;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'badge',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The comments that belong to the user.
    */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The achievements of a user.
    */
    public function achievements()
    {
        return $this->hasMany(UserAchievements::class);
    }

    /**
     * The lessons that a user has access to.
     */
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class);
    }

    /**
     * The lessons that a user has watched.
     */
    public function watched()
    {
        return $this->belongsToMany(Lesson::class)->wherePivot('watched', true);
    }

    /**
     * The next badge a user can get.
     */
    public function getNextBadgeAttribute() 
    {
        $badge = $this->badge;
        $badge_attribute = array_flip(AchievementBadgesAttributes::$title);
        $current_badge = $badge_attribute[$badge];
        $next_available = null;

        if (isset(AchievementBadgesAttributes::$next_available[$current_badge])) {
            $attrib = AchievementBadgesAttributes::$next_available[$current_badge];
            $next_available = AchievementBadgesAttributes::$title[$attrib];
        }

        return $next_available;
    }

     /**
     * The number of achievements needed till next badge.
     */
    public function getCountToNextBadgeAttribute() 
    {
        $next_badge = $this->next_badge;

        $count = 0;

        if ($next_badge != null) {

            $badge_attribute = array_flip(AchievementBadgesAttributes::$title);

            $badge = $badge_attribute[$next_badge];
    
            if (isset(AchievementBadgesAttributes::$count[$badge])) {
                $count = AchievementBadgesAttributes::$count[$badge];
                $count =  $count - count($this->achievements);
            }

        }

        return $count;
    }

}
