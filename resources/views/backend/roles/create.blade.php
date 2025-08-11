@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Create Role</h1>
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <form action="{{ route('backend.roles.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>
        <div class="form-group">
            <label for="permissions">Permissions</label>
            @foreach($permissions as $category => $categoryPermissions)
                <h5>{{ $category }}</h5>
                @foreach($categoryPermissions as $permission)
                    <div class="form-check">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="form-check-input">
                        <label class="form-check-label">{{ $permission->name }}</label>
                    </div>
                @endforeach
            @endforeach
        </div>
        <button type="submit" class="btn btn-primary">Create Role</button>
    </form>
</div>

<style>
    .container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background-color: #ffffff;
    }

    h1 {
        text-align: center;
        color: #333333;
        margin-bottom: 20px;
    }

    .alert {
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #555555;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #cccccc;
        border-radius: 4px;
        font-size: 16px;
    }

    .form-check {
        margin-bottom: 10px;
    }

    .form-check-input {
        margin-right: 10px;
    }

    .form-check-label {
        font-size: 16px;
        color: #555555;
    }

    h5 {
        margin-top: 20px;
        margin-bottom: 10px;
        color: #333333;
    }

    .btn-primary {
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 4px;
        background-color: #007bff;
        color: #ffffff;
        font-size: 16px;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }
</style>
@endsection
