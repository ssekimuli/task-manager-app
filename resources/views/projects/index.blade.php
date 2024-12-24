@extends('layout')

@section('content')
<h1 class="mb-4">Projects</h1>
<form method="POST" action="/projects" class="form-inline mb-3">
    @csrf
    <div class="form-group mr-2">
        <input type="text" name="name" placeholder="Project Name" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Add Project</button>
</form>

<ul class="list-group">
    @foreach($projects as $project)
    <li class="list-group-item">
        <a href="/projects/{{ $project->id }}/tasks">{{ $project->name }}</a>
    </li>
    @endforeach
</ul>
@endsection
