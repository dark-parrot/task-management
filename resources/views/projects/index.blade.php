@extends(auth()->user()->role === 'admin' ? 'layout.admin_layout' : 'layout.user_layout')

@section('title', 'Projects - Task Manager')

@section('content')
<div class="dashboard-header">
    <h1>Projects</h1>
    <p>Manage and track your projects</p>
</div>

<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('projects.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Create Project
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <h5 class="card-title">
        <i class="fas fa-filter me-2"></i>Filter Projects
    </h5>
    <form action="{{ route('projects.index') }}" method="GET" class="row g-3">
        <div class="col-md-4">
            <label for="search" class="form-label">Search</label>
            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by title or description">
        </div>
        <div class="col-md-2">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="">All Statuses</option>
                <option value="todo" {{ request('status') == 'todo' ? 'selected' : '' }}>To Do</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Done</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="priority" class="form-label">Priority</label>
            <select class="form-select" id="priority" name="priority">
                <option value="">All Priorities</option>
                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="sort" class="form-label">Sort By</label>
            <select class="form-select" id="sort" name="sort">
                <option value="created_desc" {{ request('sort') == 'created_desc' ? 'selected' : '' }}>Newest First</option>
                <option value="created_asc" {{ request('sort') == 'created_asc' ? 'selected' : '' }}>Oldest First</option>
                <option value="due_date_asc" {{ request('sort') == 'due_date_asc' ? 'selected' : '' }}>Due Date (Ascending)</option>
                <option value="due_date_desc" {{ request('sort') == 'due_date_desc' ? 'selected' : '' }}>Due Date (Descending)</option>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
        </div>
    </form>
</div>

@if(count($projects) > 0)
    <div class="row">
        @foreach($projects as $project)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">
                                <a href="{{ route('projects.show', $project->id) }}" class="text-decoration-none">
                                    {{ $project->title }}
                                </a>
                            </h5>
                            <span class="badge bg-{{ $project->priority === 'high' ? 'danger' : ($project->priority === 'medium' ? 'warning' : 'success') }}">
                                {{ ucfirst($project->priority) }}
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <span class="badge bg-{{ $project->status === 'todo' ? 'warning' : ($project->status === 'in_progress' ? 'primary' : ($project->status === 'done' ? 'success' : ($project->status === 'rejected' ? 'danger' : 'secondary'))) }}">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                        </div>
                        
                        <p class="mb-3" style="height: 48px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                            {{ $project->description ?? 'No description provided.' }}
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span><i class="fas fa-user me-1"></i> {{ $project->owner_name }}</span>
                            @if($project->due_date)
                                <span><i class="fas fa-calendar me-1"></i> {{ \Carbon\Carbon::parse($project->due_date)->format('M d, Y') }}</span>
                            @endif
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-primary me-2">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <a href="{{ route('projects.show', $project->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye me-1"></i> View
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-folder-open fa-4x mb-3" style="color: var(--accent-blue); opacity: 0.5;"></i>
            <h4>No Projects Found</h4>
            <p>You don't have any projects yet. Create your first project to get started.</p>
            <a href="{{ route('projects.create') }}" class="btn btn-primary mt-3">
                <i class="fas fa-plus me-1"></i> Create Project
            </a>
        </div>
    </div>
@endif
@endsection