<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\UserAchievements;
use App\Models\User;
use App\Events\BadgeUnlocked;
use App\Options\AchievementBadgesAttributes;
use App\Options\BadgeAchievementCount;

class ProcessUserBadgeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var UserAchievements
    */
    public $user_achievement;

    /**
     * @var User
    */
    public $user;

    /**
     * @var String
    */
    public $badge_name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UserAchievements $user_achievement, User $user)
    {
        $this->user_achievement = $user_achievement;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $number_of_achievements = $this->user->achievements->count();

        if (isset(BadgeAchievementCount::$choices[$number_of_achievements])) {

            //fetch badge title attribute from enum
            $title_attribute = BadgeAchievementCount::$choices[$number_of_achievements];

            //fetch badge title
            $this->badge_name = AchievementBadgesAttributes::$title[$title_attribute];
            
            BadgeUnlocked::dispatch($this->badge_name, $this->user);

        } else {
            return false;
        }
    }
}
