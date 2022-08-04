<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Options\LessonsAchievementAttributes;
use App\Options\CommentAchievementAttributes;
use App\Options\AchievementTypes;
use App\Models\User;
use App\Models\UserAchievements;

class UserAchievementsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserAchievements::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        //get achievement attribute class
        $class_name = $this->faker->randomElement(AchievementTypes::$title);

        $achievement_attribute = $this->values($class_name);

        return [
            'user_id'          => User::factory(),
            'achievement_type' => $achievement_attribute['achievement_type'],
            'achievement_name' =>  $achievement_attribute['achievement_name'],
        ];
    }

     /**
     * Indicate the achievement type user achievement falls under.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function type(string $type = null)
    {
        return $this->state(function () use ($type) {
            $type= strtolower($type);
            switch ($type) {
                case 'comment':
                    $type = CommentAchievementAttributes::ACHIEVEMENT_TYPE; 
                    $name = $this->faker->randomElement(CommentAchievementAttributes::$title);
                    break;
                case 'lesson':
                    $type = LessonsAchievementAttributes::ACHIEVEMENT_TYPE; 
                    $name = $this->faker->randomElement(LessonsAchievementAttributes::$title);
                    break;
                default:
                    $type = CommentAchievementAttributes::ACHIEVEMENT_TYPE; 
                    $name = $this->faker->randomElement(CommentAchievementAttributes::$title);
            }

            return [
                'achievement_type' => $type,
                'achievement_name' => $name,
            ];
        });
    }

     /**
     * Indicate the achievement type user achievement falls under.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function values(string $type = null)
    {
        $type= strtolower($type);
        switch ($type) {
            case 'comment':
                $type = CommentAchievementAttributes::ACHIEVEMENT_TYPE; 
                $name = $this->faker->randomElement(CommentAchievementAttributes::$title);
                break;
            case 'lesson':
                $type = LessonsAchievementAttributes::ACHIEVEMENT_TYPE; 
                $name = $this->faker->randomElement(LessonsAchievementAttributes::$title);
                break;
            default:
                $type = CommentAchievementAttributes::ACHIEVEMENT_TYPE; 
                $name = $this->faker->randomElement(CommentAchievementAttributes::$title);
        }

        return [
            'achievement_type' => $type,
            'achievement_name' => $name,
        ];
    }
}
