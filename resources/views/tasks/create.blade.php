@extends(auth()->user()->role === 'admin' ? 'layout.admin_layout' : 'layout.user_layout')

@section('title', 'Create Task - Task Manager')

@section('styles')
<style>
    .required-label::after {
        content: " *";
        color: var(--accent-red);
    }
</style>
@endsection

@section('content')
<div class="dashboard-header">
    <h1>Create Task</h1>
    <p>Add a new task to your workflow</p>
</div>

<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('tasks.index') }}" class="btn btn-primary">
        <i class="fas fa-arrow-left me-1"></i> Back to Tasks
    </a>
</div>

<div class="card">
    <h5 class="card-title">
        <i class="fas fa-tasks me-2"></i>Task Details
    </h5>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf
        
        <div class="row mb-3">
            <div class="col-md-8">
                <label for="title" class="form-label required-label">Task Title</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
            </div>
            
            <div class="col-md-4">
                <label for="status" class="form-label required-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="todo" {{ old('status') == 'todo' ? 'selected' : '' }}>To Do</option>
                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>Done</option>
                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="project_id" class="form-label required-label">Project</label>
                <select class="form-select" id="project_id" name="project_id" required>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id', request('project_id')) == $project->id ? 'selected' : '' }}>
                            {{ $project->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-6">
                <label for="user_id" class="form-label required-label">Assigned To</label>
                <select class="form-select" id="user_id" name="user_id" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="priority" class="form-label required-label">Priority</label>
                <select class="form-select" id="priority" name="priority" required>
                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }} selected>Medium</option>
                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>

                </select>
            </div>
            
            <div class="col-md-4">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date') }}">
            </div>
            
            <div class="col-md-4">
                <label for="estimated_minutes" class="form-label">Estimated Time (minutes)</label>
                <input type="number" class="form-control" id="estimated_minutes" name="estimated_minutes" value="{{ old('estimated_minutes') }}" min="0">
            </div>
        </div>
        
        <div class="mb-3">
            <label for="tags" class="form-label">Tags</label>
            <input type="text" class="form-control" id="tags" name="tags" value="{{ old('tags') }}" placeholder="Enter tags separated by commas">
            <div class="form-text text-muted">Example: design, frontend, urgent</div>
        </div>
        
        <div class="mb-3">
            <label for="repeat_cycle" class="form-label">Repeat Cycle (days)</label>
            <input type="number" class="form-control" id="repeat_cycle" name="repeat_cycle" value="{{ old('repeat_cycle', 0) }}" min="0">
            <div class="form-text text-muted">Set to 0 for no repetition, or enter number of days for recurring tasks</div>
        </div>
        
        <div class="d-flex justify-content-end mt-4">
            <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Create Task
            </button>
        </div>
    </form>
</div>
@endsection