<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Event;
use Illuminate\Support\Facades\Bus;
use App\Models\User;
use App\Models\Lesson;
use App\Events\LessonWatched;
use App\Events\AchievementUnlocked;
use Illuminate\Support\Facades\Queue;
use App\Options\LessonsAchievementAttributes;
use App\Jobs\ProcessUserWatchLessonOperationJob;

class LessonWatchedTest extends TestCase
{
    use RefreshDatabase;


    public function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
    }

    /**
     * Tetst that the 
     *
     * @return void
     */
    public function test_that_the_event_is_listened_for()
    {
        Queue::fake();

        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $user->watched()->attach($lesson, ['watched' => 1]);

        event(new LessonWatched($lesson, $user));
        
        Queue::assertPushed(function (ProcessUserWatchLessonOperationJob $job) use ($lesson, $user) {
            return $job->lesson->id === $lesson->id && $job->user->id  === $user->id;
        });
    }

    /**
     * Test that achievement unlocked event is triggered on first lesson watched 
     *
     * @return void
    */
    public function test_achievement_unlocked_triggered_on_first_lesson_watched()
    {
        Event::fake();

        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $user->watched()->attach($lesson, ['watched' => 1]);

        $achievement_name = LessonsAchievementAttributes::$title['FIRST_LESSON_WATCHED'];

        event(new LessonWatched($lesson, $user));

        ProcessUserWatchLessonOperationJob::dispatch($lesson, $user);

        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($achievement_name, $user) {
            return $event->achievement_name === $achievement_name && $event->user->id === $user->id;
        });

    }

     /**
     * Test that achievement unlocked event is not triggered when user watches similar lesson twice
     *
     * @return void
    */
    public function test_achievement_unlocked_not_triggered_when_user_watches_a_lesson_twice()
    {
        Event::fake();

        $user = User::factory()->create();
        $lessons = Lesson::factory(4)->create();

        $user->watched()->attach($lessons, ['watched' => 1]);

        //watch first lesson again
        $user->watched()->attach($lessons[0], ['watched' => 1]);

        $achievement_name = LessonsAchievementAttributes::$title['FIVE_LESSONS_WATCHED'];

        event(new LessonWatched($lessons[0], $user));

        ProcessUserWatchLessonOperationJob::dispatch($lessons[0], $user);

        Event::assertNotDispatched(AchievementUnlocked::class, function ($event) use ($achievement_name, $user) {
            return $event->achievement_name === $achievement_name && $event->user->id === $user->id;
        });

    }

     /**
     * Test that achievement unlocked event is triggered on first lesson watched and then on fifth lesson watched 
     *
     * @return void
     */
    public function test_achievement_event_triggered_only_on_first_lesson_and_fifth_lesson_watched()
    {
        $user = User::factory()->create();
        $achievement_name = [
            1 => LessonsAchievementAttributes::$title['FIRST_LESSON_WATCHED'],
            5 => LessonsAchievementAttributes::$title['FIVE_LESSONS_WATCHED']
        ];

        for ($i=1; $i<=6; $i++) {

            Event::fake();

            $lesson = Lesson::factory()->create();

            $user->watched()->attach($lesson, ['watched' => 1]);

            event(new LessonWatched($lesson, $user));

            ProcessUserWatchLessonOperationJob::dispatch($lesson, $user);

            if ( $i === 1 || $i === 5) {
                Event::assertDispatched(function (AchievementUnlocked $event) use ($achievement_name, $user, $i) {
                    return $event->user->id === $user->id && $event->achievement_name == $achievement_name[$i];
                });
            } else {
                Event::assertNotDispatched(function (AchievementUnlocked $event) use ($achievement_name, $user) {
                     return $event->user->id === $user->id;
                });
            }
        }

    }

}
