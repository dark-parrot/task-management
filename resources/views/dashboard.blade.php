@extends(auth()->user()->role === 'admin' ? 'layout.admin_layout' : 'layout.user_layout')

@section('title', 'Dashboard - Task Manager')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="dashboard-header">
    <h1>Dashboard</h1>
    <p>Welcome back, {{ Auth::user()->name }}!</p>
</div>

<!-- Stats Cards -->
<div class="stats-container">
    <div class="stat-card">
        <i class="fas fa-folder fa-2x mb-2"></i>
        <p>TOTAL PROJECTS</p>
        <h2>{{ $projects }}</h2>
    </div>
    <div class="stat-card">
        <i class="fas fa-check-circle fa-2x mb-2"></i>
        <p>COMPLETED TASKS</p>
        <h2>{{ $completedTasks }}</h2>
    </div>
    <div class="stat-card">
        <i class="fas fa-spinner fa-2x mb-2"></i>
        <p>PENDING TASKS</p>
        <h2>{{ $pendingTasks }}</h2>
    </div>
    <div class="stat-card">
        <i class="fas fa-tasks fa-2x mb-2"></i>
        <p>TOTAL TASKS</p>
        <h2>{{ $totalTasks }}</h2>
    </div>
</div>

<!-- Charts Row -->
<div class="chart-container">
    <div class="card">
        <h5 class="card-title">
            <i class="fas fa-chart-line me-2"></i>
            Task Activity (Last 7 Days)
        </h5>
        <div class="chart-container-inner">
            <canvas id="taskActivityChart" height="300"></canvas>
        </div>
    </div>
    
    <div class="card">
        <h5 class="card-title">
            <i class="fas fa-chart-pie me-2"></i>
            Task Status Distribution
        </h5>
        <div class="chart-container-inner">
            <canvas id="taskStatusChart" height="300"></canvas>
        </div>
    </div>
</div>

<!-- Tasks Row -->
<div class="tasks-container">
    <div class="card">
        <h5 class="card-title">
            <i class="fas fa-calendar-alt me-2"></i>
            Upcoming Tasks
        </h5>
        @if(count($upcomingTasks) > 0)
            <ul class="task-list">
                @foreach($upcomingTasks as $task)
                    <li class="task-item">
                        <div class="task-title">{{ $task->title }}</div>
                        <div class="task-meta">
                            <span><i class="far fa-folder me-1"></i> {{ $task->project_title ?? 'No Project' }}</span>
                            <span><i class="far fa-calendar me-1"></i> {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="text-center mt-4">
                <a href="{{ route('tasks.index') }}" class="btn btn-primary">View All Tasks</a>
            </div>
        @else
            <div class="text-center text-muted my-4">
                <i class="far fa-calendar-check fa-3x mb-3"></i>
                <p>No upcoming tasks</p>
                <a href="{{ route('tasks.create') }}" class="btn btn-primary mt-2">Create New Task</a>
            </div>
        @endif
    </div>
    
    <div class="card">
        <h5 class="card-title">
            <i class="fas fa-history me-2"></i>
            Recent Activity
        </h5>
        @if(count($recentTasks) > 0)
            <ul class="task-list">
                @foreach($recentTasks as $task)
                    <li class="task-item">
                        <div class="task-title">{{ $task->title }}</div>
                        <div class="task-meta">
                            <span><i class="far fa-folder me-1"></i> {{ $task->project_title ?? 'No Project' }}</span>
                            <span><i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($task->updated_at)->diffForHumans() }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="text-center mt-4">
                <a href="{{ route('tasks.create') }}" class="btn btn-success">Create New Task</a>
            </div>
        @else
            <div class="text-center text-muted my-4">
                <i class="far fa-clock fa-3x mb-3"></i>
                <p>No recent activity</p>
                <a href="{{ route('tasks.create') }}" class="btn btn-success mt-2">Create New Task</a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Chart.js global defaults for dark theme
    Chart.defaults.color = 'rgba(255, 255, 255, 0.7)';
    Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';
    
    // Task Activity Chart
    const taskActivityCtx = document.getElementById('taskActivityChart').getContext('2d');
    const taskActivityChart = new Chart(taskActivityCtx, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [
                {
                    label: 'Created Tasks',
                    data: @json($created),
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.2)',
                    borderWidth: 3,
                    pointBackgroundColor: '#4e73df',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#4e73df',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Completed Tasks',
                    data: @json($completed),
                    borderColor: '#01b574',
                    backgroundColor: 'rgba(1, 181, 116, 0.2)',
                    borderWidth: 3,
                    pointBackgroundColor: '#01b574',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#01b574',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(20, 30, 70, 0.8)',
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    },
                    padding: 15,
                    cornerRadius: 10,
                    displayColors: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)'
                    },
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
    
    // Task Status Chart
    const taskStatusCtx = document.getElementById('taskStatusChart').getContext('2d');
    const taskStatusChart = new Chart(taskStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['To Do', 'In Progress', 'Done', 'Pending'],
            datasets: [{
                data: [{{ $todo }}, {{ $inProgress }}, {{ $done }}, {{ $pending }}],
                backgroundColor: [
                    '#ffb547',
                    '#4e73df',
                    '#01b574',
                    '#7928ca'
                ],
                borderColor: 'rgba(20, 30, 70, 0.5)',
                borderWidth: 2,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(20, 30, 70, 0.8)',
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    },
                    padding: 15,
                    cornerRadius: 10,
                    displayColors: false
                }
            }
        }
    });
</script>
@endsection