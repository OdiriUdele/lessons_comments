<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    public function index(User $user)
    {
        $unlocked_achievments = $user->achievements->pluck('achievement_name')->toArray();

        $next_badge = $user->next_badge;

        //get next available achievements;
        $next_available_achievements = $user->achievements->sortByDesc('created_at')->unique('achievement_type');

        $next_available_achievements = $next_available_achievements->where('next_available_achievement','<>',null)->pluck('next_available_achievement');

        return response()->json([
            'unlocked_achievements' => $unlocked_achievments,
            'next_available_achievements' => $next_available_achievements,
            'current_badge' => $user->badge,
            'next_badge' => $next_badge,
            'remaing_to_unlock_next_badge' => $user->count_to_next_badge
        ]);
    }
}
