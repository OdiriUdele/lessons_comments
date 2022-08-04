<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Mockery;
use Event;
use Illuminate\Support\Facades\Bus;
use App\Models\User;
use App\Models\Comment;
use App\Events\CommentWritten;
use App\Events\AchievementUnlocked;
use Illuminate\Support\Facades\Queue;
use App\Options\CommentAchievementAttributes;
use App\Jobs\ProcessUserCommentOperationJob;

class CommentWrittenTest extends TestCase
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

        $comment = Comment::factory()->create();

        event(new CommentWritten($comment));
        
        Queue::assertPushed(function (ProcessUserCommentOperationJob $job) use ($comment) {
            return $job->comment->id === $comment->id;
        });
    }

     /**
     * Test that achievement unlocked event is triggered on first comment 
     *
     * @return void
     */
    public function test_for_first_user_comment_event()
    {
        Event::fake();

        $comment = Comment::factory()->create();
        $achievement_name = 'First Comment Written';

        $comment_user = $comment->user;

        event(new CommentWritten($comment));

        ProcessUserCommentOperationJob::dispatch($comment)->handle();


        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($achievement_name, $comment_user) {
            return $event->achievement_name === $achievement_name && $event->user->id === $comment_user->id;
        });

    }

     /**
     * Test that achievement unlocked event is not triggered on second comment 
     *
     * @return void
     */
    public function test_achievement_event_not_triggered_on_user_with_2_comments()
    {
        Event::fake();
        $user = User::factory()->create();

        $comment = Comment::factory(2)->for($user)->create();
        $comment_user = $user;

        $achievement_name = 'First Comment Written';

        event(new CommentWritten($comment[0]));

        ProcessUserCommentOperationJob::dispatch($comment[0])->handle();

        Event::assertNotDispatched(AchievementUnlocked::class, function ($event) use ($achievement_name, $comment_user) {
            return $event->user->id === $comment_user->id;
        });

    }

     /**
     * Test that achievement unlocked event is triggered on first comment 
     *
     * @return void
     */
    public function test_achievement_event_not_triggered_after_first_comment_until_third_comment()
    {
        $user = User::factory()->create();
        $achievement_name = [
            1 => CommentAchievementAttributes::$title['FIRST_COMMENT_WRITTEN'],
            3 => CommentAchievementAttributes::$title['THREE_COMMENTS_WRITTEN']
        ];

        for ($i=1; $i<=3; $i++) {
            Event::fake();
            $comment = Comment::factory()->for($user)->create();
            event(new CommentWritten($comment));
            ProcessUserCommentOperationJob::dispatch($comment);

            if ( $i === 1 || $i === 3) {
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
