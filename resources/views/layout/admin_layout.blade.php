<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dark_theme.css') }}">
    @yield('styles')
    <title>@yield('title', 'Admin Dashboard')</title>
</head>
<body>
    <div class="dashboard">
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
                        <a href="{{ route('admin.dashboard') }}" class="menu-title">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard Overview
                        </a>
                    </div>

                    <div class="menu-item has-children">
                        <button class="menu-title">
                            <i class="fas fa-folder"></i>
                            Projects
                        </button>
                        <div class="submenu">
                            <a href="{{ route('projects.index') }}">All Projects</a>
                            <a href="{{ route('projects.create') }}">Create Project</a>
                        </div>
                    </div>

                    <div class="menu-item has-children">
                        <button class="menu-title">
                            <i class="fas fa-tasks"></i>
                            Tasks
                        </button>
                        <div class="submenu">
                            <a href="{{ route('tasks.index') }}">All Tasks</a>
                            <a href="{{ route('tasks.create') }}">Create Task</a>
                        </div>
                    </div>

                    <div class="menu-item has-children">
                        <button class="menu-title">
                            <i class="fas fa-users"></i>
                            Users
                        </button>
                        <div class="submenu">
                            <a href="{{ route('admin.users.index') }}">All Users</a>
                            <a href="{{ route('admin.users.create') }}">Create User</a>
                        </div>
                    </div>

                    {{-- <div class="menu-item has-children">
                        <button class="menu-title">
                            <i class="fas fa-chart-line"></i>
                            Reports
                        </button>
                        <div class="submenu">
                            <a href="{{ route('admin.dashboard') }}">Task Status Summary</a>
                            <a href="{{ route('admin.dashboard') }}">Time Tracking</a>
                        </div>
                    </div> --}}

                    <div class="menu-item has-children">
                        <button class="menu-title">
                            <i class="fas fa-cog"></i>
                            Settings
                        </button>
                        <div class="submenu">
                            <a href="{{ route('profile') }}">Profile Settings</a>
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden-form">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="rightpanel">
            <div class="navbar">
                <button id="sidebarToggle" class="btn btn-link d-md-none" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h4>@yield('PageName')</h4>
                <form action="{{ route('search') }}" method="GET" class="search-bar">
                    <input type="text" name="q" id="searchText" placeholder="Search tasks & projects..." value="{{ request('q') }}">
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
                
                @yield('mainboard')
                @yield('content')
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
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
        
        // Mobile sidebar toggle
        function toggleSidebar() {
            const leftPanel = document.querySelector('.leftpanel');
            leftPanel.classList.toggle('show');
        }
        
        // Search functionality
        function performSearch() {
            const searchText = document.getElementById('searchText').value;
            // Implement your search functionality here
            console.log('Searching for:', searchText);
        }
    </script>
    @yield('scripts')
</body>
</html>