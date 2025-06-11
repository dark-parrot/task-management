<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dark_theme.css') }}">
</head>
<body class="auth-page">
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <div class="logo-pulse mb-4">
                    <i class="fas fa-tasks fa-3x logo-icon"></i>
                </div>
                <h2>Task Manager</h2>
                <p>Create a new account</p>
            </div>
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text auth-input-icon">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Enter your full name">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text auth-input-icon">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required placeholder="Enter your email">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text auth-input-icon">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="Create a password">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text auth-input-icon">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Confirm your password">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-user-plus me-2"></i> Register
                </button>
            </form>
            
            <div class="register-link">
                <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
            </div>
        </div>
    </div>

    <!-- Floating elements for visual effect -->
    <div class="floating floating-top-left">
        <i class="fas fa-chart-line fa-4x floating-icon-blue"></i>
    </div>
    <div class="floating floating-bottom-right">
        <i class="fas fa-tasks fa-4x floating-icon-purple"></i>
    </div>
    <div class="floating floating-middle-right">
        <i class="fas fa-project-diagram fa-4x floating-icon-cyan"></i>
    </div>
</body>
</html>