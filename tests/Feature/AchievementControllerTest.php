<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserAchievements;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Options\AchievementBadgesAttributes;

class AchievementControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test to check the route.
     *
     * @return void
     */
    public function test_the_route_is_functional()
    {
        $user = User::factory()->create();
        $user_achievements = UserAchievements::factory()
                            ->for($user)->create([
                                'achievement_type' => 'comment',
                                'achievement_name' => 'First Comment Written'
                            ]);
        
        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertStatus(200);
    }

    /**
     * A basic test return 404.
     *
     * @return void
     */
    public function test_the_route_returns_404_for_invalid_user()
    {
        $user = User::factory()->create();
        $user_achievements = UserAchievements::factory(3)
                            ->for($user)->create();
        
        $response = $this->get("/users/10/achievements");

        $response->assertStatus(404);
    }

    /**
     * A basic test to see if the correct badge is returned
     *
     * @return void
     */
    public function test_the_route_returns_correct_user_badge()
    {
        $user = User::factory()->beginner()->create();
        $user_achievements = UserAchievements::factory(3)
                            ->for($user)->create();
        
        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertStatus(200);

        $response->assertJson([
            'current_badge'                => 'Beginner',
            'next_badge'                   => 'Intermediate',
            'remaing_to_unlock_next_badge' => 1
        ], false);
    }

    /**
     * A basic test to see if the correct badge is returned
     *
     * @return void
     */
    public function test_the_route_returns_correct_json()
    {
        $badge = AchievementBadgesAttributes::$title[array_rand(AchievementBadgesAttributes::$title)];
        
        $user = User::factory()
                ->badge($badge)
                ->create();
        $user_achievements = UserAchievements::factory(3)
                            ->for($user)->create();
        $unlocked_achievments = $user->achievements->pluck('achievement_name')->toArray();
        
        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertStatus(200);

        $response->assertJson([
            'unlocked_achievements'        => $unlocked_achievments,
            'current_badge'                => $badge,
            'next_badge'                   => $user->next_badge,
            'remaing_to_unlock_next_badge' => $user->count_to_next_badge
        ], false);
    }

    /**
     * A basic test to check the route.
     *
     * @return void
     */
    public function test_the_route_returns_remaining_to_unlock_next_badge_as_1()
    {
        $user = User::factory()->beginner()->create();
        $user_achievements = UserAchievements::factory(3)
                            ->for($user)->create();
        
        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertStatus(200);

        $response->assertJson([
            'current_badge'                => 'Beginner',
            'next_badge'                   => 'Intermediate',
            'remaing_to_unlock_next_badge' => 1
        ], false);
    }

     /**
     * A basic test to check the route returns the remaing_to_unlock_next_badge as 4;
     *
     * @return void
     */
    public function test_the_route_returns_remaining_to_unlock_next_badge_as_4()
    {
        $user = User::factory()->badge('Intermediate')->create();
        $user_achievements = UserAchievements::factory(4)
                            ->for($user)->create();
        
        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertStatus(200);

        $response->assertJson([
            'current_badge'                => 'Intermediate',
            'next_badge'                   => 'Advanced',
            'remaing_to_unlock_next_badge' => 4
        ], false);
    }
            
}
