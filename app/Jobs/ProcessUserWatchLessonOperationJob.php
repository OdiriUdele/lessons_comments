<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Lesson;
use App\Models\User;
use App\Events\AchievementUnlocked;
use App\Options\LessonsAchievementAttributes;
use App\Options\LessonAchievementAttributesCount;

class ProcessUserWatchLessonOperationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * @var Lesson
    */
    public $lesson;

    /**
     * @var User
    */
    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Lesson $lesson, User $user)
    {
        $this->lesson = $lesson;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        $user_watched_lessons = $this->user->watched->groupBy('id')->count();

        //check if it is an already watched lesson
        $already_watched = $this->alreadyWatchedLesson($this->lesson);

        if (!$already_watched) {
            
            if (isset(LessonAchievementAttributesCount::$choices[$user_watched_lessons])) {

                 //fetch achievement title attribute form enum
                $title_attribute = LessonAchievementAttributesCount::$choices[$user_watched_lessons];

                //fetch achievement title
                $this->achievement_name = LessonsAchievementAttributes::$title[$title_attribute];

                AchievementUnlocked::dispatch($this->achievement_name, $this->user);
            } else {
                return false;
            }

        }

      
    }

    public function alreadyWatchedLesson(Lesson $lesson) {
        $times_watched = $this->user->watched->where('id',$lesson->id)->count();

        return $times_watched > 1 ? true : false;
    }
}
