@extends('layout.admin_layout')

@section('title', 'Admin Dashboard - Task Manager')

@section('PageName', 'Admin Dashboard')

@section('styles')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
@endsection

@section('content')
    <div class="container-fluid py-4 dashboard-wrapper">
        <div class="dashboard-header">
            <h1>Dashboard Overview</h1>
            <p>Welcome to your admin dashboard. Here's an overview of your task management system.</p>
        </div>
        
        <div class="stats-container">
            <div class="stat-card">
                <i class="fas fa-folder fa-2x mb-2"></i>
                <p>TOTAL PROJECTS</p>
                <h2>{{ $totalProjects }}</h2>
            </div>
            <div class="stat-card">
                <i class="fas fa-tasks fa-2x mb-2"></i>
                <p>ACTIVE TASKS</p>
                <h2>{{ $activeTasks }}</h2>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <p>COMPLETED TASKS</p>
                <h2>{{ $completedTasks }}</h2>
            </div>
            <div class="stat-card">
                <i class="fas fa-users fa-2x mb-2"></i>
                <p>TOTAL USERS</p>
                <h2>{{ $registeredUsers }}</h2>
            </div>
        </div>
        
        <div class="chart-container">
            <div class="card">
                <h5 class="card-title">
                    <i class="fas fa-chart-line me-2"></i>Task Activity (Last 7 Days)
                </h5>
                <div style="height: 320px; position: relative; width: 100%;">
                    <canvas id="taskActivityChart" width="400" height="320"></canvas>
                </div>
            </div>
            <div class="card">
                <h5 class="card-title">
                    <i class="fas fa-chart-pie me-2"></i>Task Status
                </h5>
                <div style="height: 320px; position: relative; width: 100%;">
                    <canvas id="taskStatusChart" width="400" height="320"></canvas>
                </div>
            </div>
        </div>
        
        <div class="tasks-container">
            <div class="card">
                <h5 class="card-title">
                    <i class="fas fa-calendar-alt me-2"></i>Upcoming Tasks
                </h5>
                @if(count($upcomingTasks) > 0)
                    <ul class="task-list">
                        @foreach($upcomingTasks as $task)
                            <li class="task-item">
                                <div class="task-title">{{ $task->title }}</div>
                                <div class="task-meta">
                                    <span><i class="far fa-calendar me-1"></i> {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</span>
                                    <span><i class="fas fa-tag me-1"></i> {{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center text-muted my-4">
                        <i class="far fa-calendar-check fa-3x mb-3"></i>
                        <p>No upcoming tasks in the next 7 days</p>
                    </div>
                @endif
            </div>
            <div class="card">
                <h5 class="card-title">
                    <i class="fas fa-chart-bar me-2"></i>Tasks per Project
                </h5>
                <div style="height: 320px; position: relative; width: 100%;">
                    <canvas id="projectTasksChart" width="400" height="320"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Store PHP data in JavaScript variables
        var chartLabels = @json($labels);
        var chartCreated = @json($created);
        var chartCompleted = @json($completed);
        var chartTodo = {{ $todo ?? 0 }};
        var chartDoing = {{ $doing ?? 0 }};
        var chartDone = {{ $done ?? 0 }};
        var chartProjects = @json($projects);
        var chartTasksPerProject = @json($tasksPerProject);
        
        // Fallback data in case PHP data is not available
        if (!chartLabels || chartLabels.length === 0) {
            chartLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            chartCreated = [3, 5, 2, 7, 4, 6, 8];
            chartCompleted = [2, 4, 1, 5, 3, 4, 6];
        }
        
        if (chartTodo === 0 && chartDoing === 0 && chartDone === 0) {
            chartTodo = 5;
            chartDoing = 10;
            chartDone = 15;
        }
        
        if (!chartProjects || chartProjects.length === 0) {
            chartProjects = ['Project A', 'Project B', 'Project C', 'Project D'];
            chartTasksPerProject = [12, 8, 15, 6];
        }
        
        // Chart.js global defaults for dark theme
        Chart.defaults.color = 'rgba(255, 255, 255, 0.7)';
        Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';
        
        // Initialize charts when DOM is loaded
        $(document).ready(function() {
            // Line Chart - Task Activity
            var lineChart = new Chart(
                document.getElementById('taskActivityChart').getContext('2d'),
                {
                    type: 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [
                            {
                                label: 'Tasks Created',
                                data: chartCreated,
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
                                label: 'Tasks Completed',
                                data: chartCompleted,
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
                }
            );
            
            // Pie Chart - Task Status
            var pieChart = new Chart(
                document.getElementById('taskStatusChart').getContext('2d'),
                {
                    type: 'doughnut',
                    data: {
                        labels: ['To Do', 'In Progress', 'Done'],
                        datasets: [{
                            data: [chartTodo, chartDoing, chartDone],
                            backgroundColor: [
                                '#ffb547',
                                '#4e73df',
                                '#01b574'
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
                }
            );
            
            // Bar Chart - Tasks per Project
            var barChart = new Chart(
                document.getElementById('projectTasksChart').getContext('2d'),
                {
                    type: 'bar',
                    data: {
                        labels: chartProjects,
                        datasets: [{
                            label: 'Number of Tasks',
                            data: chartTasksPerProject,
                            backgroundColor: [
                                'rgba(78, 115, 223, 0.7)',
                                'rgba(121, 40, 202, 0.7)',
                                'rgba(3, 216, 243, 0.7)',
                                'rgba(1, 181, 116, 0.7)',
                                'rgba(255, 0, 128, 0.7)',
                                'rgba(255, 181, 71, 0.7)'
                            ],
                            borderWidth: 0,
                            borderRadius: 5,
                            maxBarThickness: 40
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
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
                }
            );
        });
    </script>
@endsection