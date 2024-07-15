<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use App\Models\Earning;
use App\Models\UserTask;
use Illuminate\Support\Facades\Auth;

class UserTaskController extends Controller
{
    public function fetchTasks()
    {
        $user = Auth::user();

        // Get tasks that are not completed by the user
        $tasks = Task::whereNotIn('id', $user->tasks()->pluck('task_id')->toArray())->get();

        return response()->json($tasks, 200);
    }

    public function completeTask(Request $request, Task $task)
    {
        $user = Auth::user();

        // Check if the user has already completed the task
        if ($user->tasks()->where('task_id', $task->id)->exists()) {
            return response()->json(['error' => 'Task already completed'], 400);
        }

        // Attach the task to the user
        $user->tasks()->attach($task->id);

        // Update user's balance with task reward
        $user->balance += $task->reward;
        $user->save();

        // Create an Earning record for the user
        Earning::create([
            'user_id' => $user->id,
            'amount' => $task->reward,
            'description' => 'ad', // Example: 'ad', 'direct', 'level' as per your requirement
        ]);

        // Distribute earnings to referral levels if applicable
        $this->distributeEarnings($user, $task->reward);

        return response()->json(['message' => 'Task completed successfully'], 200);
    }

    protected function distributeEarnings(User $user, $amount)
    {
        $referrer = $user->referrer;

        $level = 1;
        $levelLimit =7;
        $percentages = 0.1; // Example percentages for 3 levels

        while ($referrer && $level <= $levelLimit) {
            $earningAmount = $amount * $percentages;

            Earning::create([
                'user_id' => $referrer->id,
                'amount' => $earningAmount,
                'description' => 'level',
            ]);

            $referrer = $referrer->referrer;
            $level++;
        }
    }
}
