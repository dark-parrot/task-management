@extends('layout.admin_layout')

@section('title', 'Create User - Task Manager')

@section('PageName', 'Create New User')

@section('content')
<div class="dashboard-header">
    <h1>Create New User</h1>
    <p>Add a new user to the system</p>
</div>

<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
        <i class="fas fa-arrow-left me-1"></i> Back to Users
    </a>
</div>

<div class="card">
    <h5 class="card-title">
        <i class="fas fa-user-plus me-2"></i>User Details
    </h5>
    
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
        </div>
        
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="d-flex justify-content-end mt-4">
            <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Create User
            </button>
        </div>
    </form>
</div>
@endsection