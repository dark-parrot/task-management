<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Task Manager')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dark_theme.css') }}">
    @yield('styles')
</head>
<body>
    <div class="dashboard">
        <!-- Left Panel / Sidebar -->
        <div class="leftpanel">
            <div class="profile-card">
                <div class="image-container">
                    <div class="image">
                        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=4e73df&color=fff" alt="{{ Auth::user()->name }}">
                    </div>
                    <h4>{{ Auth::user()->name }}</h4>
                    <p>{{ ucfirst(Auth::user()->role) }}</p>
                    <div class="user-modal">
                        <h4>{{ Auth::user()->name }}</h4>
                        <p>Role: {{ ucfirst(Auth::user()->role) }}</p>
                        <p>Email: {{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>
            
            <div class="options-card">
                <div class="sidebar">
                    <div class="menu-item">
                        <a href="{{ route('dashboard') }}" class="menu-title {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                    </div>
                    
                    <div class="menu-item has-children {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                        <button class="menu-title">
                            <i class="fas fa-folder"></i>
                            Projects
                        </button>
                        <div class="submenu">
                            <a href="{{ route('projects.index') }}" class="{{ request()->routeIs('projects.index') ? 'active' : '' }}">All Projects</a>
                        </div>
                    </div>
                    
                    <div class="menu-item has-children {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                        <button class="menu-title">
                            <i class="fas fa-tasks"></i>
                            Tasks
                        </button>
                        <div class="submenu">
                            <a href="{{ route('tasks.index') }}" class="{{ request()->routeIs('tasks.index') ? 'active' : '' }}">My Tasks</a>
                        </div>
                    </div>
                    
                    <div class="menu-item">
                        <a href="{{ route('profile') }}" class="menu-title {{ request()->routeIs('profile') ? 'active' : '' }}">
                            <i class="fas fa-user"></i>
                            Profile
                        </a>
                    </div>
                    
                    <div class="menu-item">
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="menu-title">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden-form">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Panel -->
        <div class="rightpanel">
            <!-- Navbar -->
            <div class="navbar">
                <button id="sidebarToggle" class="btn btn-link d-md-none" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                
                <h4>@yield('title', 'Dashboard')</h4>
                
                <form action="{{ route('search') }}" method="GET" class="search-bar">
                    <input type="text" name="q" placeholder="Search tasks & projects..." value="{{ request('q') }}">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
                
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-nav').submit();">
                    <button class="logout-button">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </button>
                </a>
                <form id="logout-form-nav" action="{{ route('logout') }}" method="POST" class="hidden-form">
                    @csrf
                </form>
            </div>
            
            <!-- Main Content -->
            <div class="mainboard">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Mobile sidebar toggle
        function toggleSidebar() {
            const leftPanel = document.querySelector('.leftpanel');
            leftPanel.classList.toggle('show');
        }
        
        // Add active class to menu items
        document.addEventListener('DOMContentLoaded', function() {
            // Get current URL path
            const path = window.location.pathname;
            
            // Find all menu items
            const menuItems = document.querySelectorAll('.menu-item');
            
            // Loop through menu items
            menuItems.forEach(item => {
                const links = item.querySelectorAll('a');
                
                // Check if any link in this menu item matches the current path
                links.forEach(link => {
                    if (link.getAttribute('href') === path || path.includes(link.getAttribute('href'))) {
                        // Add active class to parent menu item
                        item.classList.add('active');
                        
                        // If it's a submenu item, show the submenu
                        if (item.classList.contains('has-children')) {
                            const submenu = item.querySelector('.submenu');
                            if (submenu) {
                                submenu.style.display = 'block';
                            }
                        }
                    }
                });
            });
        });
    </script>
    @yield('scripts')
</body>
</html>