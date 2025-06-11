@extends(auth()->user()->role === 'admin' ? 'layout.admin_layout' : 'layout.user_layout')

@section('title', 'My Tasks - Task Manager')

@section('content')
<div class="dashboard-header">
    <h1>My Tasks</h1>
    <p>Manage and track your tasks</p>
</div>

<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('tasks.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Create Task
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <h5 class="card-title">
        <i class="fas fa-filter me-2"></i>Filter Tasks
    </h5>
    <form action="{{ route('tasks.index') }}" method="GET" class="row g-3">
        <div class="col-md-3">
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
        <div class="col-md-3">
            <label for="project" class="form-label">Project</label>
            <select class="form-select" id="project" name="project_id">
                <option value="">All Projects</option>
                @foreach($projects ?? [] as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->title }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
        </div>
    </form>
</div>

@if(count($tasks) > 0)
    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                        <tr>
                            <td>
                                <a href="{{ route('tasks.show', $task->id) }}" class="task-title">{{ $task->title }}</a>
                                <div class="task-meta">{{ $task->project_title ?? 'No Project' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'success') }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $task->status === 'todo' ? 'warning' : ($task->status === 'in_progress' ? 'primary' : ($task->status === 'done' ? 'success' : ($task->status === 'rejected' ? 'danger' : 'secondary'))) }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </td>
                            <td>
                                @if($task->due_date)
                                    @php
                                        $dueDate = \Carbon\Carbon::parse($task->due_date);
                                        $isOverdue = $dueDate->isPast() && $task->status != 'done';
                                    @endphp
                                    <span class="{{ $isOverdue ? 'text-danger' : '' }}">
                                        {{ $dueDate->format('M d, Y') }}
                                        @if($isOverdue)
                                            <i class="fas fa-exclamation-circle ms-1" title="Overdue"></i>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted">No due date</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-sm btn-info me-2">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-primary me-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="event.preventDefault(); 
                                            document.getElementById('delete-task-{{ $task->id }}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-task-{{ $task->id }}" action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-tasks fa-4x mb-3" style="color: var(--accent-blue); opacity: 0.5;"></i>
            <h4>No Tasks Found</h4>
            <p>You don't have any tasks yet. Create your first task to get started.</p>
            <a href="{{ route('tasks.create') }}" class="btn btn-primary mt-3">
                <i class="fas fa-plus me-1"></i> Create Task
            </a>
        </div>
    </div>
@endif
@endsection