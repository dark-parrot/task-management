@extends('layout.admin_layout')

@section('title', 'Manage Users - Task Manager')

@section('PageName', 'User Management')

@section('content')
<div class="dashboard-header">
    <h1>Manage Users</h1>
    <p>View and manage all users in the system</p>
</div>

<div class="d-flex justify-content-between mb-4">
    <div class="search-container">
        <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Search users..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-primary">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus me-1"></i> Add New User
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger mb-4">
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <h5 class="card-title">
        <i class="fas fa-users me-2"></i>User List
    </h5>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-{{ $user->role === 'admin' ? 'primary' : 'secondary' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}</td>
                        <td>
                            <div class="d-flex">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary me-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                @if(auth()->id() != $user->id)
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection