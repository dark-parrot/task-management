@extends(auth()->user()->role === 'admin' ? 'layout.admin_layout' : 'layout.user_layout')

@section('title', $project->title . ' - Task Manager')

@section('styles')
<style>
    .project-header {
        background: var(--glass-bg);
        backdrop-filter: var(--glass-blur);
        -webkit-backdrop-filter: var(--glass-blur);
        border: var(--glass-border);
        box-shadow: var(--glass-shadow);
        border-radius: var(--border-radius);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    
    .project-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--gradient-primary);
    }
    
    .project-title {
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }
    
    .project-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        font-size: 0.9rem;
        color: var(--text-secondary);
    }
    
    .meta-item i {
        margin-right: 0.5rem;
        width: 16px;
        text-align: center;
        color: var(--accent-blue);
    }
    
    .status-todo { 
        background-color: var(--accent-yellow); 
        color: #212529;
    }
    
    .status-in_progress { 
        background-color: var(--accent-blue); 
    }
    
    .status-done { 
        background-color: var(--accent-green); 
    }
    
    .status-pending { 
        background-color: #858796; 
    }
    
    .status-rejected { 
        background-color: var(--accent-red); 
    }
    
    .priority-high { 
        background-color: var(--accent-red); 
    }
    
    .priority-medium { 
        background-color: var(--accent-yellow); 
        color: #212529; 
    }
    
    .priority-low { 
        background-color: var(--accent-green); 
    }
    
    .team-member {
        display: inline-flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50px;
        padding: 0.25rem 0.75rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
        border: var(--glass-border);
        transition: all 0.3s ease;
    }
    
    .team-member:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .team-member img {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        margin-right: 0.5rem;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }
    
    .task-item {
        border-left: 4px solid;
        margin-bottom: 0.75rem;
        border-radius: var(--border-radius);
        background: var(--glass-bg);
        backdrop-filter: var(--glass-blur);
        -webkit-backdrop-filter: var(--glass-blur);
        border-top: var(--glass-border);
        border-right: var(--glass-border);
        border-bottom: var(--glass-border);
        transition: all 0.3s ease;
    }
    
    .task-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .task-item-high {
        border-left-color: var(--accent-red);
    }
    
    .task-item-medium {
        border-left-color: var(--accent-yellow);
    }
    
    .task-item-low {
        border-left-color: var(--accent-green);
    }
    
    .task-header {
        padding: 0.75rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .task-title {
        font-weight: 600;
        margin-bottom: 0;
        color: var(--text-primary);
        max-width: 330px;
    }
    
    .task-body {
        padding: 0.75rem 1rem;
        color: var(--text-secondary);
    }
    
    .task-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        font-size: 0.85rem;
        color: var(--text-secondary);
    }
    
    .task-meta i {
        color: var(--accent-blue);
    }
    
    .modal-content {
        background: var(--secondary-bg);
        border: var(--glass-border);
    }
    
    .modal-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .modal-footer {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .btn-close {
        color: var(--text-primary);
        text-shadow: none;
        opacity: 0.7;
    }
    
    .btn-close:hover {
        opacity: 1;
    }
</style>
@endsection

@section('content')
<div class="dashboard-header">
    <h1>Project Details</h1>
    <p>View and manage project information</p>
</div>

<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-primary me-2">
        <i class="fas fa-edit me-1"></i> Edit Project
    </a>
    <a href="{{ route('projects.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-1"></i> Back to Projects
    </a>
</div>

<div class="project-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h2 class="project-title">{{ $project->title }}</h2>
            <div class="project-meta">
                <div class="meta-item">
                    <i class="fas fa-user"></i>
                    <span>Owner: {{ $project->owner_name }}</span>
                </div>
                @if($project->due_date)
                <div class="meta-item">
                    <i class="fas fa-calendar"></i>
                    <span>Due: {{ \Carbon\Carbon::parse($project->due_date)->format('M d, Y') }}</span>
                </div>
                @endif
                <div class="meta-item">
                    <i class="fas fa-clock"></i>
                    <span>Created: {{ \Carbon\Carbon::parse($project->created_at)->format('M d, Y') }}</span>
                </div>
                @if($project->estimated_minutes)
                <div class="meta-item">
                    <i class="fas fa-hourglass-half"></i>
                    <span>Estimated: {{ $project->estimated_minutes }} minutes</span>
                </div>
                @endif
            </div>
        </div>
        <div class="d-flex">
            <span class="badge priority-{{ $project->priority }} me-2">
                {{ ucfirst($project->priority) }} Priority
            </span>
            <span class="badge status-{{ $project->status }}">
                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
            </span>
        </div>
    </div>
    
    @if($project->description)
    <div class="mt-3">
        <h6 class="fw-bold text-primary">Description</h6>
        <p>{{ $project->description }}</p>
    </div>
    @endif
    
    @if($project->tags)
    <div class="mt-3">
        <h6 class="fw-bold text-primary">Tags</h6>
        <div>
            @foreach(explode(',', $project->tags) as $tag)
                <span class="badge bg-secondary me-1">{{ trim($tag) }}</span>
            @endforeach
        </div>
    </div>
    @endif
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card mb-4">
            <h5 class="card-title">
                <i class="fas fa-users me-2"></i>Team Members
            </h5>
            @if(count($teamMembers) > 0)
                <div>
                    @foreach($teamMembers as $member)
                        <div class="team-member">
                            <img src="https://ui-avatars.com/api/?name={{ $member->name }}&background=4e73df&color=fff" alt="{{ $member->name }}">
                            <span>{{ $member->name }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-muted my-3">
                    <i class="fas fa-users fa-3x mb-3" style="opacity: 0.3;"></i>
                    <p>No team members assigned</p>
                </div>
            @endif
            
            <div class="text-center mt-3">
                <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-user-plus me-1"></i> Manage Team
                </a>
            </div>
        </div>
        
        <div class="card">
            <h5 class="card-title">
                <i class="fas fa-cog me-2"></i>Project Actions
            </h5>
            <div class="d-grid gap-2">
                <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" class="btn btn-success">
                    <i class="fas fa-plus me-1"></i> Add Task
                </a>
                <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit Project
                </a>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteProjectModal">
                    <i class="fas fa-trash me-1"></i> Delete Project
                </button>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card">
            <h5 class="card-title d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-tasks me-2"></i>Project Tasks
                </div>
                <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus me-1"></i> Add Task
                </a>
            </h5>
            
            @if(count($tasks) > 0)
                @foreach($tasks as $task)
                    <div class="task-item task-item-{{ strtolower($task->priority) }}">
                        <div class="task-header">
                            <h6 class="task-title">{{ $task->title }}</h6>
                            <div>
                                <span class="badge status-{{ $task->status }} me-1">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                                <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        <div class="task-body">
                            <div class="task-meta">
                                <div><i class="fas fa-user me-1"></i> {{ $task->assigned_to }}</div>
                                @if($task->due_date)
                                    <div><i class="fas fa-calendar me-1"></i> {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</div>
                                @endif
                                @if($task->estimated_minutes)
                                    <div><i class="fas fa-hourglass-half me-1"></i> {{ $task->estimated_minutes }} min</div>
                                @endif
                            </div>
                            @if($task->description)
                                <div class="mt-2 small">{{ \Illuminate\Support\Str::limit($task->description, 100) }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-4">
                    <i class="fas fa-tasks fa-3x mb-3" style="opacity: 0.3;"></i>
                    <h5>No Tasks Found</h5>
                    <p class="text-muted">This project doesn't have any tasks yet.</p>
                    <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" class="btn btn-success mt-2">
                        <i class="fas fa-plus me-1"></i> Add First Task
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Project Modal -->
<div class="modal fade" id="deleteProjectModal" tabindex="-1" aria-labelledby="deleteProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteProjectModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this project? This action cannot be undone and will also delete all tasks associated with this project.</p>
                <p class="fw-bold text-danger">Project: {{ $project->title }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('projects.destroy', $project->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Project</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection