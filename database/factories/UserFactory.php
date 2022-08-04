<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Options\AchievementBadgesAttributes;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'badge' => $this->faker->randomElement(AchievementBadgesAttributes::$title),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

     /**
     * Indicate that the user badge is that of a beginner.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function beginner()
    {
        return $this->state(function (array $attributes) {
            return [
                'badge' => AchievementBadgesAttributes::$title[AchievementBadgesAttributes::BEGINNER],
            ];
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function badge($badge = null)
    {
        return $this->state(function (array $attributes) use ($badge) {
            return [
                'badge' => $badge,
            ];
        });
    }
}
