@extends(auth()->user()->role === 'admin' ? 'layout.admin_layout' : 'layout.user_layout')

@section('title', 'Create Project - Task Manager')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Select2 Dark Theme Styling */
    .form-control {
        background-color: #131a41;
        /* border: var(--glass-border);
        border-radius: 10px;
        color: var(--text-primary); */
    }

    .select2-container--default .select2-selection--multiple,
    .select2-container--default .select2-selection--single {
        background-color: #131a41;
        border: var(--glass-border);
        border-radius: 10px;
        min-height: 38px;
        color: var(--text-primary);
    }
    
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: rgba(78, 115, 223, 0.5);
        box-shadow: 0 0 15px rgba(78, 115, 223, 0.2);
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #131a41;
        border: none;
        color: white;
        border-radius: 20px;
        padding: 2px 10px;
        margin-top: 5px;
        margin-right: 5px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 5px;
        border-right: none;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        /* background-color: rgba(255, 255, 255, 0.2); */
        background-color: #131a41;
        color: white;
    }
    
    .select2-dropdown {
        /* background-color: var(--secondary-bg); */
        background-color: #131a41;
        border: var(--glass-border);
        border-radius: 10px;
        box-shadow: var(--glass-shadow);
        z-index: 9999;
    }
    
    .select2-container--default .select2-results__option--highlighted[aria-selected],
    .select2-container--default .select2-results__option--highlighted[data-selected] {
        background-color: #131a41;
        color: white;
    }
    
    .select2-container--default .select2-results__option[aria-selected=true],
    .select2-container--default .select2-results__option[data-selected=true] {
        background-color: rgba(78, 115, 223, 0.3);
    }
    
    .select2-container--default .select2-results__option {
        background-color: #131a41;
        color: white;
        cursor: pointer;
        padding: 8px 12px;
        transition: all 0.2s ease;
    }
    
    .select2-container--default .select2-search--dropdown .select2-search__field {
        background: #131a41;
        border: var(--glass-border);
        color: var(--text-primary);
        border-radius: 5px;
        padding: 8px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--text-primary);
        line-height: 38px;
        padding-left: 12px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: var(--text-secondary) transparent transparent transparent;
    }
    
    .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-color: transparent transparent var(--text-secondary) transparent;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        padding: 5px 8px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__clear {
        color: var(--text-secondary);
        margin-top: 5px;
        margin-right: 5px;
    }
    
    .select2-container--open .select2-dropdown {
        animation: fadeInSelect 0.2s ease;
    }
    
    @keyframes fadeInSelect {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Required field indicator */
    .required-label::after {
        content: " *";
        color: var(--accent-red);
    }

    .dark-select-dropdown {
        background-color: #131a41 !important;
        border: var(--glass-border);
        border-radius: 10px;
        box-shadow: var(--glass-shadow);
    }
</style>
@endsection

@section('content')
<div class="dashboard-header">
    <h1>Create Project</h1>
    <p>Create a new project to organize your tasks</p>
</div>

<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('projects.index') }}" class="btn btn-primary">
        <i class="fas fa-arrow-left me-1"></i> Back to Projects
    </a>
</div>

<div class="card">
    <h5 class="card-title">
        <i class="fas fa-folder-plus me-2"></i>Project Details
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
    
    <form action="{{ route('projects.store') }}" method="POST">
        @csrf
        
        <div class="row mb-3">
            <div class="col-md-8">
                <label for="title" class="form-label required-label">Project Title</label>
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
            <label for="team_members" class="form-label">Team Members</label>
            <select class="form-control select2" id="team_members" name="team_members[]" multiple>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ in_array($user->id, old('team_members', [])) ? 'selected' : '' }} style="background-color: #131a41;">
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
            <div class="form-text text-muted">Select team members who will work on this project</div>
        </div>
        
        <div class="mb-3">
            <label for="repeat_cycle" class="form-label">Repeat Cycle (days)</label>
            <input type="number" class="form-control" id="repeat_cycle" name="repeat_cycle" value="{{ old('repeat_cycle', 0) }}" min="0">
            <div class="form-text text-muted">Set to 0 for no repetition, or enter number of days for recurring projects</div>
        </div>
        
        <div class="d-flex justify-content-end mt-4">
            <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Create Project
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select team members",
            allowClear: true,
            theme: "classic",
            dropdownParent: $('body')
        }).on('select2:open', function() {
            // Add custom class to the dropdown for additional styling
            setTimeout(function() {
                $('.select2-dropdown').addClass('dark-select-dropdown');
            }, 0);
        });
    });
</script>
@endsection