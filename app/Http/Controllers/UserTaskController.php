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

        // Fetch only incomplete tasks for the user
        $incompleteTasks = Task::whereDoesntHave('userTasks', function ($query) use ($user) {
            $query->where('user_id', $user->id)->whereNotNull('completed_at');
        })->get();

        return response()->json($incompleteTasks, 200);
    }

    public function completeTask(Request $request, Task $task)
    {
        $user = Auth::user();

        if ($user->tasks()->where('task_id', $task->id)->exists()) {
            return response()->json(['error' => 'Task already completed'], 400);
        }

        // Mark the task as completed for the user
        $userTask = new UserTask([
            'user_id' => $user->id,
            'task_id' => $task->id,
            'completed_at' => now(),
        ]);
        $userTask->save();

        // Create Earning entry for the user
        Earning::create([
            'user_id' => $user->id,
            'amount' => $task->reward,
            'description' => 'ad',
        ]);

        // Distribute earnings to referral levels
        $this->distributeEarnings($user, $task->reward);

        return response()->json(['message' => 'Task completed'], 200);
    }

    protected function distributeEarnings(User $user, $amount)
    {
        $referrer = $user->referrer;

        $level = 1;
        $percentages = [0.10, 0.05, 0.03]; // Example percentages for 3 levels

        while ($referrer && $level <= count($percentages)) {
            $earningAmount = $amount * $percentages[$level - 1];

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
