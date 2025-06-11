@extends(auth()->user()->role === 'admin' ? 'layout.admin_layout' : 'layout.user_layout')

@section('title', 'Edit Project - Task Manager')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d1d3e2;
        border-radius: 0.35rem;
        min-height: 38px;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #bac8f3;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }
    .required-label::after {
        content: " *";
        color: #e74a3b;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Edit Project</h1>
        @if(auth()->user()->role === 'admin')
            <p class="text-muted">Update project details and team members</p>
        @else
            <p class="text-muted">Update project status</p>
        @endif
    </div>
    <div>
        <a href="{{ route('projects.show', $project->id) }}" class="btn btn-outline-primary">
            <i class="fas fa-eye me-1"></i> View Project
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
        
        <form action="{{ route('projects.update', $project->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            @if(auth()->user()->role === 'admin')
                <!-- Admin can edit all fields -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label for="title" class="form-label required-label">Project Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $project->title) }}" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="status" class="form-label required-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="todo" {{ old('status', $project->status) == 'todo' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ old('status', $project->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="done" {{ old('status', $project->status) == 'done' ? 'selected' : '' }}>Done</option>
                            <option value="pending" {{ old('status', $project->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="rejected" {{ old('status', $project->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $project->description) }}</textarea>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="priority" class="form-label required-label">Priority</label>
                        <select class="form-select" id="priority" name="priority" required>
                            <option value="low" {{ old('priority', $project->priority) == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $project->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $project->priority) == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date', $project->due_date ? \Carbon\Carbon::parse($project->due_date)->format('Y-m-d') : '') }}">
                    </div>
                    
                    <div class="col-md-4">
                        <label for="estimated_minutes" class="form-label">Estimated Time (minutes)</label>
                        <input type="number" class="form-control" id="estimated_minutes" name="estimated_minutes" value="{{ old('estimated_minutes', $project->estimated_minutes) }}" min="0">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="tags" class="form-label">Tags</label>
                    <input type="text" class="form-control" id="tags" name="tags" value="{{ old('tags', $project->tags) }}" placeholder="Enter tags separated by commas">
                    <div class="form-text">Example: design, frontend, urgent</div>
                </div>
                
                <div class="mb-3">
                    <label for="team_members" class="form-label">Team Members</label>
                    <select class="form-control select2" id="team_members" name="team_members[]" multiple>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ in_array($user->id, old('team_members', $teamMembers)) ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Select team members who will work on this project</div>
                </div>
                
                @if(count($teamMembers) > 0)
                <div class="mb-3">
                    <label class="form-label">Current Team Members</label>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($project->users as $member)
                                <tr>
                                    <td>{{ $member->name }}</td>
                                    <td>{{ $member->email }}</td>
                                    <td>
                                        <form action="{{ route('projects.remove-user', ['project' => $project->id, 'user' => $member->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to remove this user from the project?')">
                                                <i class="fas fa-user-minus"></i> Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                
                <div class="mb-3">
                    <label for="repeat_cycle" class="form-label">Repeat Cycle (days)</label>
                    <input type="number" class="form-control" id="repeat_cycle" name="repeat_cycle" value="{{ old('repeat_cycle', $project->repeat_cycle) }}" min="0">
                    <div class="form-text">Set to 0 for no repetition, or enter number of days for recurring projects</div>
                </div>
            @else
                <!-- Regular users can only change status -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h4 style="color: white;">Project: {{ $project->title }}</h4>
                        <p>{{ $project->description }}</p>
                        <hr>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6 mx-auto">
                        <label for="status" class="form-label required-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="todo" {{ old('status', $project->status) == 'todo' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ old('status', $project->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="done" {{ old('status', $project->status) == 'done' ? 'selected' : '' }}>Done</option>
                            <option value="pending" {{ old('status', $project->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="rejected" {{ old('status', $project->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        <div class="form-text text-center mt-2">As a regular user, you can only update the project status.</div>
                    </div>
                </div>
                
                <!-- Add hidden fields to maintain the current values -->
                <input type="hidden" name="title" value="{{ $project->title }}">
                <input type="hidden" name="description" value="{{ $project->description }}">
                <input type="hidden" name="priority" value="{{ $project->priority }}">
                <input type="hidden" name="due_date" value="{{ $project->due_date }}">
                <input type="hidden" name="estimated_minutes" value="{{ $project->estimated_minutes }}">
                <input type="hidden" name="tags" value="{{ $project->tags }}">
                <input type="hidden" name="repeat_cycle" value="{{ $project->repeat_cycle }}">
                
                @if(isset($teamMembers) && is_array($teamMembers))
                    @foreach($teamMembers as $memberId)
                        <input type="hidden" name="team_members[]" value="{{ $memberId }}">
                    @endforeach
                @endif
            @endif
            
            <div class="d-flex justify-content-between mt-4">
                @if(auth()->user()->role === 'admin')
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteProjectModal">
                        <i class="fas fa-trash me-1"></i> Delete Project
                    </button>
                @else
                    <div></div> <!-- Empty div to maintain flex spacing -->
                @endif
                
                <div>
                    <a href="{{ route('projects.show', $project->id) }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Project</button>
                </div>
            </div>
        </form>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select team members",
            allowClear: true
        });
    });
</script>
@endsection