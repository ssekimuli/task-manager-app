<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index($projectId)
    {
        $project = Project::findOrFail($projectId);
        $tasks = $project->tasks()->orderBy('priority')->get();
        $allProjects = Project::all();

        return view('tasks.index', compact('project', 'tasks', 'allProjects'));
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

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $task->update([
            'name' => $request->input('name'),
        ]);

        return redirect()->back()->with('success', 'Task updated successfully.');
    }


    public function destroy(Task $task)
    {
        $task->delete();
        return back();
    }
}
