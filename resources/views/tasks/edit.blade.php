@extends(auth()->user()->role === 'admin' ? 'layout.admin_layout' : 'layout.user_layout')

@section('title', 'Edit Task - Task Manager')

@section('styles')
<style>
    .required-label::after {
        content: " *";
        color: #e74a3b;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Edit Task</h1>
        @if(auth()->user()->role === 'admin')
            <p class="text-muted">Update task details and status</p>
        @else
            <p class="text-muted">Update task status</p>
        @endif
    </div>
    <div>
        <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-outline-primary">
            <i class="fas fa-eye me-1"></i> View Task
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('tasks.update', $task->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            @if(auth()->user()->role === 'admin')
                <!-- Admin can edit all fields -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label for="title" class="form-label required-label">Task Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $task->title) }}" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="status" class="form-label required-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="todo" {{ old('status', $task->status) == 'todo' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="done" {{ old('status', $task->status) == 'done' ? 'selected' : '' }}>Done</option>
                            <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="rejected" {{ old('status', $task->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $task->description) }}</textarea>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="project_id" class="form-label required-label">Project</label>
                        <select class="form-select" id="project_id" name="project_id" required>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="user_id" class="form-label required-label">Assigned To</label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', $task->user_id) == $user->id ? 'selected' : '' }}>
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
                            <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date', $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '') }}">
                    </div>
                    
                    <div class="col-md-4">
                        <label for="estimated_minutes" class="form-label">Estimated Time (minutes)</label>
                        <input type="number" class="form-control" id="estimated_minutes" name="estimated_minutes" value="{{ old('estimated_minutes', $task->estimated_minutes) }}" min="0">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="tags" class="form-label">Tags</label>
                    <input type="text" class="form-control" id="tags" name="tags" value="{{ old('tags', $task->tags) }}" placeholder="Enter tags separated by commas">
                    <div class="form-text">Example: design, frontend, urgent</div>
                </div>
                
                <div class="mb-3">
                    <label for="repeat_cycle" class="form-label">Repeat Cycle (days)</label>
                    <input type="number" class="form-control" id="repeat_cycle" name="repeat_cycle" value="{{ old('repeat_cycle', $task->repeat_cycle) }}" min="0">
                    <div class="form-text">Set to 0 for no repetition, or enter number of days for recurring tasks</div>
                </div>
            @else
                <!-- Regular users can only change status -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h4 style="color: white;">Task: {{ $task->title }}</h4>
                        <p>{{ $task->description }}</p>
                        <hr>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6 mx-auto">
                        <label for="status" class="form-label required-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="todo" {{ old('status', $task->status) == 'todo' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="done" {{ old('status', $task->status) == 'done' ? 'selected' : '' }}>Done</option>
                            <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="rejected" {{ old('status', $task->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        <div class="form-text text-center mt-2">As a regular user, you can only update the task status.</div>
                    </div>
                </div>
                
                <!-- Add hidden fields to maintain the current values -->
                <input type="hidden" name="title" value="{{ $task->title }}">
                <input type="hidden" name="description" value="{{ $task->description }}">
                <input type="hidden" name="project_id" value="{{ $task->project_id }}">
                <input type="hidden" name="user_id" value="{{ $task->user_id }}">
                <input type="hidden" name="priority" value="{{ $task->priority }}">
                <input type="hidden" name="due_date" value="{{ $task->due_date }}">
                <input type="hidden" name="estimated_minutes" value="{{ $task->estimated_minutes }}">
                <input type="hidden" name="tags" value="{{ $task->tags }}">
                <input type="hidden" name="repeat_cycle" value="{{ $task->repeat_cycle }}">
            @endif
            
            <div class="d-flex justify-content-between mt-4">
                @if(auth()->user()->role === 'admin')
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteTaskModal">
                        <i class="fas fa-trash me-1"></i> Delete Task
                    </button>
                @else
                    <div></div> <!-- Empty div to maintain flex spacing -->
                @endif
                
                <div>
                    <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Task</button>
                </div>
            </div>
        </form>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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