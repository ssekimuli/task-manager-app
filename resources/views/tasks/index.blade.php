@extends('layout')

@section('content')
<h1 class="mb-4">Tasks for {{ $project->name }}</h1>
<form method="POST" action="/tasks" class="form-inline mb-3">
    @csrf
    <div class="form-group mr-2">
        <input type="text" name="name" placeholder="Task Name" class="form-control" required>
    </div>
    <input type="hidden" name="project_id" value="{{ $project->id }}">
    <button type="submit" class="btn btn-primary">Add Task</button>
</form>

<ul class="list-group" id="task-list">
    @foreach($tasks as $task)
    <li class="list-group-item d-flex justify-content-between align-items-center" 
        data-id="{{ $task->id }}" 
        draggable="true">
        {{ $task->name }}
        <form method="POST" action="/tasks/{{ $task->id }}" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
        </form>
    </li>
    @endforeach
</ul>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const taskList = document.getElementById('task-list');
    let draggedItem = null;

    // Event listeners for drag-and-drop functionality
    taskList.addEventListener('dragstart', function(e) {
        draggedItem = e.target;
        e.target.style.opacity = '0.5';
    });

    taskList.addEventListener('dragend', function(e) {
        e.target.style.opacity = '';
        draggedItem = null;

        // Update task order on the server
        const tasks = Array.from(taskList.querySelectorAll('li')).map(li => li.dataset.id);
        fetch(`/projects/{{ $project->id }}/tasks/reorder`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ tasks })
        });
    });

    taskList.addEventListener('dragover', function(e) {
        e.preventDefault();
    });

    taskList.addEventListener('drop', function(e) {
        e.preventDefault();
        if (e.target.tagName === 'LI' && e.target !== draggedItem) {
            if (e.target.nextSibling === draggedItem) {
                taskList.insertBefore(draggedItem, e.target);
            } else {
                taskList.insertBefore(draggedItem, e.target.nextSibling);
            }
        }
    });
});
</script>
@endsection
