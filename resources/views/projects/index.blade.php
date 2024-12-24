@extends('layout')

@section('content')
<h1>Projects</h1>
<form method="POST" action="/projects">
    @csrf
    <input type="text" name="name" placeholder="Project Name" required>
    <button type="submit">Add Project</button>
</form>

<ul>
    @foreach($projects as $project)
    <li><a href="/projects/{{ $project->id }}/tasks">{{ $project->name }}</a></li>
    @endforeach
</ul>
@endsection
