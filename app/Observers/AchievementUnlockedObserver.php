<?php

namespace App\Observers;

use App\Jobs\ProcessUserBadgeJob;
use App\Models\UserAchievements;

class AchievementUnlockedObserver
{

    /**
     * @var User
    */
    public $user;

   /**
     * Handle the User Achievement "created" event.
     *
     * @param  \App\Models\UserAchievements $user_achievement
     * @return void
     */
    public function created(UserAchievements $user_achievement)
    {
        $this->user = $user_achievement->user;

        ProcessUserBadgeJob::dispatch($user_achievement, $this->user);
        
    }
}
