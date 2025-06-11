@extends(auth()->user()->role === 'admin' ? 'layout.admin_layout' : 'layout.user_layout')

@section('title', 'Search Results - Task Manager')

@section('content')
<div class="dashboard-header">
    <h1>Search Results</h1>
    <p>Results for: "{{ $searchTerm }}"</p>
</div>

<!-- Tasks Results -->
<div class="card mb-4">
    <h5 class="card-title">
        <i class="fas fa-tasks me-2"></i>Tasks ({{ count($tasks) }})
    </h5>
    
    @if(count($tasks) > 0)
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
                                    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-4">
            <p class="text-muted">No tasks found matching your search.</p>
        </div>
    @endif
</div>

<!-- Projects Results -->
<div class="card">
    <h5 class="card-title">
        <i class="fas fa-folder me-2"></i>Projects ({{ count($projects) }})
    </h5>
    
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
        <div class="text-center py-4">
            <p class="text-muted">No projects found matching your search.</p>
        </div>
    @endif
</div>

<div class="mt-4">
    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>
@endsection