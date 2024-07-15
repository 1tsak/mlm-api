<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;


class AdminTaskController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'reward' => 'required|numeric',
        ]);

        $task = Task::create($request->all());

        return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'reward' => 'required|numeric',
        ]);

        $task->update($request->all());

        return response()->json(['message' => 'Task updated successfully', 'task' => $task], 200);
    }
    public function deleteTask($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully'], 200);
    }

    public function list()
    {
        $tasks = Task::all();
        return response()->json($tasks, 200);
    }
}
