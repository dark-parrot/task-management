@extends(auth()->user()->role === 'admin' ? 'layout.admin_layout' : 'layout.user_layout')

@section('title', 'My Profile - Task Manager')

@section('content')
<div class="dashboard-header">
    <h1>My Profile</h1>
    <p>Manage your account information</p>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card mb-4">
            <h5 class="card-title">
                <i class="fas fa-user me-2"></i>Profile Information
            </h5>
            <div class="text-center">
                <div class="image mb-3" style="width: 120px; height: 120px; margin: 0 auto; border-radius: 50%; overflow: hidden; border: 3px solid rgba(255, 255, 255, 0.2); box-shadow: 0 0 20px rgba(78, 115, 223, 0.5);">
                    <img src="https://ui-avatars.com/api/?name={{ $user->name }}&background=4e73df&color=fff&size=200" alt="User Avatar" class="img-fluid">
                </div>
                <h5 class="mb-1" style="color: white;">{{ $user->name }}</h5>
                <span class="badge bg-{{ $user->role === 'admin' ? 'primary' : 'secondary' }} mb-3">{{ ucfirst($user->role) }}</span>
                <div class="profile-info-item mb-2">
                    <i class="fas fa-envelope me-2 text-primary"></i>{{ $user->email }}
                </div>
                <div class="profile-info-item mb-0">
                    <i class="fas fa-calendar me-2 text-primary"></i>Joined {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <h5 class="card-title">
                <i class="fas fa-chart-pie me-2"></i>Activity Summary
            </h5>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div style='color : white;'><i class="fas fa-folder me-2 text-primary"></i>Total Projects</div>
                <div class="fw-bold badge bg-primary" style="font-size: 14px;">{{ $projects }}</div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div style='color : white;'><i class="fas fa-tasks me-2 text-info"></i>Total Tasks</div>
                <div class="fw-bold badge bg-info" style="font-size: 14px;">{{ $totalTasks }}</div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div style='color : white;'><i class="fas fa-check-circle me-2 text-success"></i>Completed Tasks</div>
                <div class="fw-bold badge bg-success" style="font-size: 14px;">{{ $completedTasks }}</div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div style='color : white;'><i class="fas fa-clock me-2 text-warning"></i>Pending Tasks</div>
                <div class="fw-bold badge bg-warning" style="font-size: 14px;">{{ $pendingTasks }}</div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card mb-4">
            <h5 class="card-title">
                <i class="fas fa-edit me-2"></i>Edit Profile
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
            
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>
                
                <hr class="my-4" style="border-color: rgba(255, 255, 255, 0.1);">
                
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <div class="form-text text-muted">Leave blank to keep your current password</div>
                </div>
                
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                </div>
                
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
        
        <div class="card">
            <h5 class="card-title">
                <i class="fas fa-shield-alt me-2"></i>Account Security
            </h5>
            <div class="mb-4">
                <h5 class="text-primary">Two-Factor Authentication</h5>
                <p>Add additional security to your account using two-factor authentication.</p>
                <button class="btn btn-outline-primary" disabled>
                    <i class="fas fa-lock me-2"></i>Enable 2FA (Coming Soon)
                </button>
            </div>
            
            <div>
                <h5 class="text-primary">Sessions</h5>
                <p>Manage and log out your active sessions on other browsers and devices.</p>
                <button class="btn btn-outline-danger" disabled>
                    <i class="fas fa-sign-out-alt me-2"></i>Log Out Other Sessions (Coming Soon)
                </button>
            </div>
        </div>
    </div>
</div>
@endsection