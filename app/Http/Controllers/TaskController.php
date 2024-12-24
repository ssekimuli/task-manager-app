<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Project $project)
    {
        $tasks = $project->tasks;
        return view('tasks.index', compact('tasks', 'project'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
        ]);

        $priority = Task::where('project_id', $request->project_id)->max('priority') + 1;

        Task::create([
            'name' => $request->name,
            'priority' => $priority,
            'project_id' => $request->project_id,
        ]);

        return back();
    }

    public function updatePriority(Request $request, Project $project)
    {
        foreach ($request->tasks as $priority => $taskId) {
            Task::where('id', $taskId)->update(['priority' => $priority + 1]);
        }

        return response()->json(['message' => 'Priority updated']);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return back();
    }
}
