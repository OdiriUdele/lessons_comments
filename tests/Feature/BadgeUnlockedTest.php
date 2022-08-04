<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Event;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use App\Events\BadgeUnlocked;
use App\Observers\AchievementUnlockedObserver;
use App\Models\UserAchievements;
use App\Models\User;
use App\Options\AchievementTypes;
use App\Options\BadgeAchievementCount;
use App\Options\AchievementBadgesAttributes;
use App\Jobs\ProcessUserBadgeJob;
use App\Providers\EventServiceProvider;
use DB;

class BadgeUnlockedTest extends TestCase
{
    use RefreshDatabase;


    public function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        $this->user_with_3_achievements = User::factory()->create();  
        $user = $this->user_with_3_achievements;
        $this->user_achievements =  UserAchievements::withoutEvents(function () use ($user) {
            UserAchievements::factory(1)
            ->type(AchievementTypes::COMMENT)
            ->for($user)
            ->create();
            UserAchievements::factory(2)
            ->type(AchievementTypes::LESSON)
            ->for($user)
            ->create();
        });
        
    }

    /**
     * Test that the Achievement unlocked observer is called when a new achievement is recorded
     *
     * @return void
     */
    public function test_that_the_achievement_unlocked_observer_is_triggered_on_new_achievement()
    {
        Queue::fake();  

        $user_achievement = UserAchievements::factory()->create();
        $user = $user_achievement->user;

        (new AchievementUnlockedObserver)->created($user_achievement);

        Queue::assertPushed(function (ProcessUserBadgeJob $job) use ($user_achievement, $user) {
            return $job->user->id === $user->id && $job->user_achievement;
        });

    }

     /**
     * Test that the badge unlocked event is called for a user on his fourth achievement
     *
     * @return void
     */
    public function test_that_the_badge_unlocked_event_is_called_for_a_user_on_his_fourth_achievement()
    {
        Event::fake();  

        $user = $this->user_with_3_achievements;

        $title_attribute = BadgeAchievementCount::$choices[4];

        //fetch badge title
        $badge_name = AchievementBadgesAttributes::$title[$title_attribute];

        $user_achievement = UserAchievements::factory()->for($this->user_with_3_achievements)->create();

        (new AchievementUnlockedObserver)->created($user_achievement);
        
        Event::assertDispatched(function (BadgeUnlocked $event) use ($badge_name, $user) {
            return $event->badge_name === $badge_name && $event->user->id == $user->id;
        });
    }

    /**
     * Test that the badge unlocked event is called for a user on his fourth achievement
     *
     * @return void
    */
    public function test_that_the_badge_unlocked_event_is_not_called_for_a_user_on_his_fifth_achievement()
    {
        Event::fake();  

        $user = $this->user_with_3_achievements;

        $title_attribute = BadgeAchievementCount::$choices[4];

        //fetch badge title
        $badge_name = AchievementBadgesAttributes::$title[$title_attribute];

        $user_achievements = UserAchievements::factory(2)->for($this->user_with_3_achievements)->create();

        (new AchievementUnlockedObserver)->created($user_achievements[1]);
        
        Event::assertNotDispatched(function (BadgeUnlocked $event) use ($badge_name, $user) {
            return $event->badge_name === $badge_name && $event->user->id == $user->id;
        });
    }

     /**
     * Test that the badge unlocked event is called for a user on his fourth achievement
     *
     * @return void
    */
    public function test_that_the_badge_unlocked_event_is_only_called_for_a_user_on_his_fourth_eight_and_achievement()
    { 

        $user = $this->user_with_3_achievements;

        //fetch badge title
        $badge_name =  [
            4 => AchievementBadgesAttributes::$title['INTERMEDIATE'],
            8 => AchievementBadgesAttributes::$title['ADVANCED'],
           10 => AchievementBadgesAttributes::$title['MASTER']
        ];

        for ($i=4; $i<=10; $i++) {

            Event::fake();

            $user_achievement = UserAchievements::factory()->for($this->user_with_3_achievements)->create();

            (new AchievementUnlockedObserver)->created($user_achievement);

            if ( in_array($i, [4, 8, 10]) ) {
                Event::assertDispatched(function (BadgeUnlocked $event) use ($badge_name, $user, $i) {
                    return $event->badge_name === $badge_name[$i] && $event->user->id == $user->id;
                });
            } else {
                Event::assertNotDispatched(BadgeUnlocked::class);
            }
        }
    }
    
}
