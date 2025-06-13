<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager - Organize Your Work Efficiently</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-bg: #0f1535;
            --secondary-bg: #131a41;
            --accent-blue: #4e73df;
            --accent-purple: #7928ca;
            --accent-pink: #ff0080;
            --accent-cyan: #03d8f3;
            --accent-green: #01b574;
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.7);
            --glass-bg: rgba(20, 30, 70, 0.3);
            --glass-border: 1px solid rgba(255, 255, 255, 0.1);
            --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            --glass-blur: blur(10px);
            --border-radius: 15px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-primary);
            background: var(--primary-bg);
            overflow-x: hidden;
            overflow-y: scroll;
            /* overflow: auto;
            scrollbar-width: none;    
            -ms-overflow-style: none;  */
        }
        body::-webkit-scrollbar {
            display: none;           
        }

        
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(121, 40, 202, 0.1) 0%, rgba(15, 21, 53, 0) 70%);
            z-index: -1;
            animation: rotate 30s linear infinite;
        }
        
        body::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(78, 115, 223, 0.1) 0%, rgba(15, 21, 53, 0) 70%);
            z-index: -1;
            animation: rotate 30s linear infinite reverse;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .navbar {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border-bottom: var(--glass-border);
            box-shadow: var(--glass-shadow);
            padding: 15px 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .navbar-brand i {
            margin-right: 10px;
            color: var(--accent-blue);
            font-size: 1.8rem;
        }
        
        .navbar-toggler {
            border: none;
            color: var(--text-primary);
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 0.8)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        .nav-link {
            color: var(--text-secondary);
            font-weight: 500;
            margin: 0 10px;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: var(--text-primary);
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: linear-gradient(90deg, var(--accent-blue), var(--accent-purple));
            border: none;
            padding: 10px 25px;
            font-weight: 600;
            border-radius: 30px;
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.4);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(78, 115, 223, 0.5);
        }
        
        .btn-light {
            background: rgba(255, 255, 255, 0.1);
            border: var(--glass-border);
            color: var(--text-primary);
            padding: 10px 25px;
            font-weight: 600;
            border-radius: 30px;
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            transition: all 0.3s ease;
        }
        
        .btn-light:hover {
            background: rgba(255, 255, 255, 0.2);
            color: var(--text-primary);
            transform: translateY(-3px);
        }
        
        .btn-outline-light {
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: var(--text-primary);
            padding: 10px 25px;
            font-weight: 600;
            border-radius: 30px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
            transform: translateY(-3px);
        }
        
        .hero-section {
            background: linear-gradient(135deg, rgba(78, 115, 223, 0.1) 0%, rgba(121, 40, 202, 0.1) 100%);
            padding: 150px 0 100px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgcGF0dGVyblRyYW5zZm9ybT0icm90YXRlKDQ1KSI+PHJlY3QgaWQ9InBhdHRlcm4tYmciIHdpZHRoPSI0MDAiIGhlaWdodD0iNDAwIiBmaWxsPSJyZ2JhKDAsIDAsIDAsIDApIj48L3JlY3Q+PGNpcmNsZSBmaWxsPSJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuMDMpIiBjeD0iMjAiIGN5PSIyMCIgcj0iMSI+PC9jaXJjbGU+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCBmaWxsPSJ1cmwoI3BhdHRlcm4pIiBoZWlnaHQ9IjEwMCUiIHdpZHRoPSIxMDAlIj48L3JlY3Q+PC9zdmc+');
            opacity: 0.5;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(90deg, var(--text-primary), var(--accent-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
        }
        
        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 40px;
            color: var(--text-secondary);
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .feature-card {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border: var(--glass-border);
            box-shadow: var(--glass-shadow);
            border-radius: var(--border-radius);
            padding: 40px 30px;
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-blue), var(--accent-purple));
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
        }
        
        .feature-card:hover::before {
            opacity: 1;
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 25px;
            background: linear-gradient(45deg, var(--accent-blue), var(--accent-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }
        
        .feature-card h4 {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        
        .feature-card p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .cta-section {
            background: linear-gradient(135deg, rgba(78, 115, 223, 0.1) 0%, rgba(121, 40, 202, 0.1) 100%);
            padding: 100px 0;
            position: relative;
        }
        
        .cta-section h2 {
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 2.5rem;
            color: var(--text-primary);
        }
        
        .cta-section p {
            color: var(--text-secondary);
            font-size: 1.2rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 40px;
        }
        
        .footer {
            background: var(--secondary-bg);
            color: var(--text-primary);
            padding: 80px 0 30px;
            position: relative;
        }
        
        .footer h5 {
            font-weight: 600;
            margin-bottom: 25px;
            color: var(--text-primary);
            font-size: 1.2rem;
        }
        
        .footer p {
            color: var(--text-secondary);
            line-height: 1.7;
        }
        
        .footer-links ul {
            list-style: none;
            padding-left: 0;
        }
        
        .footer-links li {
            margin-bottom: 12px;
        }
        
        .footer-links a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .footer-links a:hover {
            color: var(--text-primary);
            transform: translateX(5px);
        }
        
        .copyright {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-secondary);
        }
        
        .section-title {
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--text-primary);
        }
        
        .section-subtitle {
            color: var(--text-secondary);
            margin-bottom: 50px;
            font-size: 1.1rem;
        }
        
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        
        .glow {
            animation: glow 3s ease-in-out infinite alternate;
        }
        
        @keyframes glow {
            from { text-shadow: 0 0 5px rgba(3, 216, 243, 0.5), 0 0 10px rgba(3, 216, 243, 0.3); }
            to { text-shadow: 0 0 10px rgba(3, 216, 243, 0.7), 0 0 20px rgba(3, 216, 243, 0.5), 0 0 30px rgba(3, 216, 243, 0.3); }
        }
        
        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.8rem;
            }
            
            .hero-subtitle {
                font-size: 1.3rem;
            }
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.3rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .cta-section h2 {
                font-size: 2rem;
            }
            
            .cta-section p {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .btn-group {
                flex-direction: column;
                width: 100%;
            }
            
            .btn-group .btn {
                margin-bottom: 10px;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-tasks glow"></i>
                Task Manager
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="btn btn-primary ms-2" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary ms-2" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-2"></i> Get Started
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="hero-title">Manage Tasks Efficiently</h1>
            <p class="hero-subtitle">Streamline your workflow, collaborate with your team, and achieve more together with our modern task management platform.</p>
            <div class="d-flex justify-content-center gap-3 btn-group">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-tachometer-alt me-2"></i> Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-rocket me-2"></i> Get Started
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i> Login
                    </a>
                @endauth
            </div>
        </div>
        
        <!-- Floating elements for visual effect -->
        <div class="floating" style="position: absolute; top: 15%; left: 15%; opacity: 0.1; z-index: 1;">
            <i class="fas fa-chart-line fa-4x" style="color: #4e73df;"></i>
        </div>
        <div class="floating" style="position: absolute; top: 70%; left: 80%; opacity: 0.1; z-index: 1; animation-delay: 1s;">
            <i class="fas fa-tasks fa-4x" style="color: #7928ca;"></i>
        </div>
        <div class="floating" style="position: absolute; top: 40%; left: 85%; opacity: 0.1; z-index: 1; animation-delay: 2s;">
            <i class="fas fa-project-diagram fa-4x" style="color: #03d8f3;"></i>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" id="features">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="section-title">Powerful Features</h2>
                <p class="section-subtitle">Everything you need to manage your tasks and projects effectively</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h4>Task Management</h4>
                        <p>Create, organize, and track tasks with ease. Set priorities, deadlines, and monitor progress in real-time.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <h4>Project Collaboration</h4>
                        <p>Work together with your team on projects. Assign tasks, share updates, and achieve goals together seamlessly.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Progress Tracking</h4>
                        <p>Monitor your progress with visual dashboards and reports. Stay on top of deadlines and milestones effortlessly.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h4>Notifications</h4>
                        <p>Never miss important updates with timely notifications about task assignments and approaching deadlines.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h4>Scheduling</h4>
                        <p>Plan your work with an intuitive calendar view. Schedule tasks and manage your time effectively with visual tools.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h4>Team Communication</h4>
                        <p>Discuss tasks and projects with built-in commenting and communication tools to keep everyone aligned.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="cta-section" id="about">
        <div class="container text-center">
            <h2>Ready to boost your productivity?</h2>
            <p>Join thousands of teams who use Task Manager to organize their work and achieve more.</p>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-tachometer-alt me-2"></i> Go to Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-rocket me-2"></i> Get Started for Free
                </a>
            @endauth
        </div>
        
        <!-- Floating elements for visual effect -->
        <div class="floating" style="position: absolute; top: 20%; right: 15%; opacity: 0.1; z-index: 1; animation-delay: 1.5s;">
            <i class="fas fa-check-circle fa-4x" style="color: #01b574;"></i>
        </div>
        <div class="floating" style="position: absolute; bottom: 20%; left: 10%; opacity: 0.1; z-index: 1; animation-delay: 0.5s;">
            <i class="fas fa-lightbulb fa-4x" style="color: #ffb547;"></i>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5>
                        <i class="fas fa-tasks me-2" style="color: #4e73df;"></i>
                        Task Manager
                    </h5>
                    <p>A powerful task and project management tool designed to help teams collaborate and achieve their goals efficiently.</p>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 footer-links">
                    <h5>Product</h5>
                    <ul>
                        <li><a href="#"><i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i> Features</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i> Pricing</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i> Integrations</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i> Updates</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 footer-links">
                    <h5>Company</h5>
                    <ul>
                        <li><a href="#"><i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i> About Us</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i> Careers</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i> Blog</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i> Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 footer-links">
                    <h5>Support</h5>
                    <ul>
                        <li><a href="#"><i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i> Help Center</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i> Documentation</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i> Community</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i> Status</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 footer-links">
                    <h5>Legal</h5>
                    <ul>
                        <li><a href="#"><i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i> Privacy Policy</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i> Terms of Service</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i> Security</a></li>
                    </ul>
                </div>
            </div>
            <div class="text-center copyright">
                <p>&copy; {{ date('Y') }} Task Manager. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add smooth scrolling to all links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Add navbar background on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(15, 21, 53, 0.9)';
                navbar.style.boxShadow = '0 8px 32px 0 rgba(0, 0, 0, 0.37)';
            } else {
                navbar.style.background = 'rgba(20, 30, 70, 0.3)';
                navbar.style.boxShadow = '0 8px 32px 0 rgba(0, 0, 0, 0.37)';
            }
        });
    </script>
</body>
</html>