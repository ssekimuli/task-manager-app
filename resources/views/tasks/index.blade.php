@extends('layout')

@section('content')
<div class="mb-4">
    <form method="GET" action="/projects/{{ $project->id }}/tasks" class="form-inline">
        <label for="project" class="mr-2">Select Project:</label>
        <select id="project" name="project" class="form-control" onchange="this.form.submit()">
            @foreach($allProjects as $proj)
                <option value="{{ $proj->id }}" {{ $proj->id == $project->id ? 'selected' : '' }}>
                    {{ $proj->name }}
                </option>
            @endforeach
        </select>
    </form>
</div>
<h1 class="mb-4">Tasks for {{ $project->name }}</h1> 
<a href="/" class="btn btn-primary mt-4 mb-4">Home</a>
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
        <div>
            <button class="btn btn-sm btn-warning edit-task-btn"
                data-id="{{ $task->id }}"
                data-name="{{ $task->name }}">
                Edit
            </button>
            <form method="POST" action="/tasks/{{ $task->id }}" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
            </form>
        </div>
    </li>
    @endforeach
</ul>

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" role="dialog" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" id="edit-task-form">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="task-name">Task Name</label>
                        <input type="text" name="name" id="task-name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>


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
                body: JSON.stringify({
                    tasks
                })
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

    // edit task
    document.addEventListener('DOMContentLoaded', function() {
        const editTaskModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
        const editTaskForm = document.getElementById('edit-task-form');
        const taskNameInput = document.getElementById('task-name');

        document.querySelectorAll('.edit-task-btn').forEach(button => {
            button.addEventListener('click', function() {
                const taskId = this.dataset.id;
                const taskName = this.dataset.name;

                editTaskForm.action = `/edit-tasks/${taskId}`;
                taskNameInput.value = taskName;

                editTaskModal.show();
            });
        });
    });
</script>
@endsection