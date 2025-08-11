<!-- resources/views/roles/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Role Details</h1>
    <p><strong>Name:</strong> {{ $role->name }}</p>
    <p><strong>Description:</strong> {{ $role->description }}</p>
    <h3>Permissions</h3>
    <ul>
        @foreach($role->permissions as $permission)
            <li>{{ $permission->name }} ({{ $permission->category ? $permission->category->name : 'Uncategorized' }})</li>
        @endforeach
    </ul>
    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning">Edit</a>
    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete</button>
    </form>
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection
