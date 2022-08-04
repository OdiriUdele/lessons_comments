<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\AchievementUnlocked;
use App\Options\CommentAchievementAttributes;
use App\Options\CommentAchievementAttributesCount;
use App\Models\Comment;
use App\Models\User;

class ProcessUserCommentOperationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Comment
    */
    public $comment;

    /**
     * @var User
    */
    public $user;

    /**
     * @var String
    */
    public $achievement_name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
        $this->user = $comment->user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        $user_comments = $this->user->comments;
        $number_of_comments = $user_comments->Count();

        if (isset(CommentAchievementAttributesCount::$choices[$number_of_comments])) {

            //fetch achievement title attribute form enum
            $title_attribute = CommentAchievementAttributesCount::$choices[$number_of_comments];

            //fetch achievement title
            $this->achievement_name = CommentAchievementAttributes::$title[$title_attribute];

            AchievementUnlocked::dispatch($this->achievement_name, $this->user);

        } else {
            return false;
        }
    }
}
