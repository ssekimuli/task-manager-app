@extends('layout')

@section('content')
<h1>Tasks for {{ $project->name }}</h1>
<form method="POST" action="/tasks">
    @csrf
    <input type="text" name="name" placeholder="Task Name" required>
    <input type="hidden" name="project_id" value="{{ $project->id }}">
    <button type="submit">Add Task</button>
</form>

<ul id="task-list">
    @foreach($tasks as $task)
    <li data-id="{{ $task->id }}">{{ $task->name }}
        <form method="POST" action="/tasks/{{ $task->id }}" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit">Delete</button>
        </form>
    </li>
    @endforeach
</ul>
<script>
document.getElementById('task-list').addEventListener('dragend', function(e) {
    let tasks = Array.from(document.querySelectorAll('#task-list li')).map(li => li.dataset.id);
    fetch('/projects/{{ $project->id }}/tasks/reorder', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({tasks}),
    });
});
</script>
@endsection
