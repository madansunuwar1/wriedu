<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permissions Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    @include('backend.script.session')

    @include('backend.script.alert')
    
</head>

<body>
    <div class="container my-5">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Permission</h2>
                @if(auth()->user()->hasPermission('permissions.create'))
    <a href="{{ route('backend.permission.create') }}" class="btn btn-success">Create Permission</a>
@endif
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Description</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($perms as $perm)
                                <tr>
                                    <td>{{ $perm->name }}</td>
                                    <td>{{ $perm->slug }}</td>
                                    <td>{{ $perm->description }}</td>
                                    <td>
                                    @if(auth()->user()->hasPermission('permissions.edit'))
        <a href="{{ route('perm.edit', $perm) }}" class="btn btn-sm btn-primary">Edit</a>
    @endif
                                        
    @if(auth()->user()->hasPermission('permissions.delete'))
        <form action="{{ route('perm.destroy', $perm) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                Delete
            </button>
        </form>
    @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No permissions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
