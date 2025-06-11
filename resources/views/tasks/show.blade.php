@extends(auth()->user()->role === 'admin' ? 'layout.admin_layout' : 'layout.user_layout')

@section('title', $task->title . ' - Task Manager')

@section('styles')
<style>
    .task-header {
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
    
    .task-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--gradient-primary);
    }
    
    .task-title {
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }
    
    .task-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
        color: var(--text-secondary);
        font-style: italic;
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
    
    .comment-item {
        border-left: 3px solid var(--accent-blue);
        padding: 0.75rem;
        margin-bottom: 1rem;
        background: rgba(255, 255, 255, 0.05);
        border-radius: var(--border-radius);
        transition: all 0.3s ease;
    }
    
    .comment-item:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .comment-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    
    .comment-user {
        font-weight: 600;
        color: var(--text-primary);
    }
    
    .comment-time {
        font-size: 0.85rem;
        color: var(--text-secondary);
    }
    
    .comment-content {
        white-space: pre-line;
        color: var(--text-secondary);
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
    <h1>Task Details</h1>
    <p>View and manage task information</p>
</div>

<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-primary me-2">
        <i class="fas fa-edit me-1"></i> Edit Task
    </a>
    <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-1"></i> Back to Tasks
    </a>
</div>

<div class="task-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h2 class="task-title">{{ $task->title }}</h2>
            <div class="task-meta">
                <div class="meta-item">
                    <i class="fas fa-user"></i>
                    <span>Assigned to: {{ $task->assigned_to }}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-folder"></i>
                    <span>Project: {{ $task->project_title ?? 'No Project' }}</span>
                </div>
                @if($task->due_date)
                <div class="meta-item">
                    <i class="fas fa-calendar"></i>
                    <span>Due: {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</span>
                </div>
                @endif
                <div class="meta-item">
                    <i class="fas fa-clock"></i>
                    <span>Created: {{ \Carbon\Carbon::parse($task->created_at)->format('M d, Y') }}</span>
                </div>
                @if($task->estimated_minutes)
                <div class="meta-item">
                    <i class="fas fa-hourglass-half"></i>
                    <span>Estimated: {{ $task->estimated_minutes }} minutes</span>
                </div>
                @endif
            </div>
        </div>
        <div class="d-flex">
            <span class="badge priority-{{ $task->priority }} me-2">
                {{ ucfirst($task->priority) }} Priority
            </span>
            <span class="badge status-{{ $task->status }}">
                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
            </span>
        </div>
    </div>
    
    @if($task->description)
    <div class="mt-3">
        <h6 class="fw-bold text-primary">Description</h6>
        <p>{{ $task->description }}</p>
    </div>
    @endif
    
    @if($task->tags)
    <div class="mt-3">
        <h6 class="fw-bold text-primary">Tags</h6>
        <div>
            @foreach(explode(',', $task->tags) as $tag)
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
                <i class="fas fa-cog me-2"></i>Task Actions
            </h5>
            <div class="d-grid gap-2">
                <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit Task
                </a>
                
                @if($task->status != 'done')
                    <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="title" value="{{ $task->title }}">
                        <input type="hidden" name="description" value="{{ $task->description }}">
                        <input type="hidden" name="project_id" value="{{ $task->project_id }}">
                        <input type="hidden" name="user_id" value="{{ $task->user_id }}">
                        <input type="hidden" name="priority" value="{{ $task->priority }}">
                        <input type="hidden" name="status" value="done">
                        <input type="hidden" name="due_date" value="{{ $task->due_date }}">
                        <input type="hidden" name="estimated_minutes" value="{{ $task->estimated_minutes }}">
                        <input type="hidden" name="tags" value="{{ $task->tags }}">
                        <input type="hidden" name="repeat_cycle" value="{{ $task->repeat_cycle }}">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check me-1"></i> Mark as Complete
                        </button>
                    </form>
                @else
                    <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="title" value="{{ $task->title }}">
                        <input type="hidden" name="description" value="{{ $task->description }}">
                        <input type="hidden" name="project_id" value="{{ $task->project_id }}">
                        <input type="hidden" name="user_id" value="{{ $task->user_id }}">
                        <input type="hidden" name="priority" value="{{ $task->priority }}">
                        <input type="hidden" name="status" value="in_progress">
                        <input type="hidden" name="due_date" value="{{ $task->due_date }}">
                        <input type="hidden" name="estimated_minutes" value="{{ $task->estimated_minutes }}">
                        <input type="hidden" name="tags" value="{{ $task->tags }}">
                        <input type="hidden" name="repeat_cycle" value="{{ $task->repeat_cycle }}">
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fas fa-undo me-1"></i> Reopen Task
                        </button>
                    </form>
                @endif
                
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteTaskModal">
                    <i class="fas fa-trash me-1"></i> Delete Task
                </button>
            </div>
        </div>
        
        @if($task->project_id)
        <div class="card">
            <h5 class="card-title">
                <i class="fas fa-folder me-2"></i>Project Information
            </h5>
            <h5 class="text-primary">{{ $task->project_title }}</h5>
            <p>View the project this task belongs to for more context and related tasks.</p>
            <a href="{{ route('projects.show', $task->project_id) }}" class="btn btn-outline-primary w-100">
                <i class="fas fa-external-link-alt me-1"></i> Go to Project
            </a>
        </div>
        @endif
    </div>
    
    <div class="col-lg-8">
        <div class="card">
            <h5 class="card-title">
                <i class="fas fa-comments me-2"></i>Comments
            </h5>
            <form action="{{ route('tasks.comments.store', $task->id) }}" method="POST" class="mb-4">
                @csrf
                <div class="mb-3">
                    <label for="content" class="form-label">Add a Comment</label>
                    <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Post Comment
                    </button>
                </div>
            </form>
            
            <hr style="border-color: rgba(255, 255, 255, 0.1);">
            
            <h5 class="mb-3">{{ count($comments) }} Comments</h5>
            
            @if(count($comments) > 0)
                @foreach($comments as $comment)
                    <div class="comment-item">
                        <div class="comment-header">
                            <div class="comment-user">{{ $comment->user_name }}</div>
                            <div class="comment-time">{{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}</div>
                        </div>
                        <div class="comment-content">{{ $comment->content }}</div>
                    </div>
                @endforeach
            @else
                <div class="text-center text-muted my-4">
                    <i class="fas fa-comments fa-3x mb-3" style="opacity: 0.3;"></i>
                    <p>No comments yet. Be the first to comment!</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Task Modal -->
<div class="modal fade" id="deleteTaskModal" tabindex="-1" aria-labelledby="deleteTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteTaskModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this task? This action cannot be undone and will also delete all comments associated with this task.</p>
                <p class="fw-bold text-danger">Task: {{ $task->title }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Task</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection