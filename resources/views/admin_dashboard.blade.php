@extends('layout.admin_layout')

@section('title')
    <title>Admin Dashboard</title>
@endsection

@section('PageName')
    Dashboard
@endsection

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card-completed { background: #d4edda; }
        .card-active { background: #cce5ff; }
        .card-overdue { background: #f8d7da; }
    </style>
@endsection

@section('mainboard')  
    <div class="bg-light">
    <div class="container py-4">

        <div class="row mb-4" >
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="h2">🗂 {{ $totalProjects }}</div>
                        <div>Total Projects</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-completed text-center">
                    <div class="card-body">
                        <div class="h2">✅ {{ $completedTasks }}</div>
                        <div>Completed Tasks</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-active text-center">
                    <div class="card-body">
                        <div class="h2">🔄 {{ $activeTasks }}</div>
                        <div>Active Tasks</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="h2">👥 {{ $registeredUsers }}</div>
                        <div>Registered Users</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Section --}}
        <div class="row mb-4">
            <div class="col-md-6 mb-4">
                <div class="card chart-card">
                    <div class="card-header">Tasks Created vs Completed (Weekly)</div>
                    <div class="card-body chart-body">
                        <canvas id="lineChart" class="chart-canvas"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card chart-card">
                    <div class="card-header">Task Status Breakdown</div>
                    <div class="card-body chart-body">
                        <canvas id="pieChart" class="chart-canvas"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card chart-card">
                    <div class="card-header">Tasks per Project (In Progress)</div>
                    <div class="card-body chart-body-bottom">
                        <canvas id="barChart" class="chart-canvas"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Activity Log --}}
        <div class="row mb-4">
            {{-- Recent Activity --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Recent Activity</div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead>
                            <tr>
                                <th>Time</th>
                                <th>User</th>
                                <th>Activity</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>2 min ago</td>
                                <td>Alice</td>
                                <td>Completed task "Design UI"</td>
                            </tr>
                            <tr>
                                <td>5 min ago</td>
                                <td>Bob</td>
                                <td>Created project "CRM System"</td>
                            </tr>
                            <tr>
                                <td>15 min ago</td>
                                <td>Admin</td>
                                <td>Assigned task to Charlie</td>
                            </tr>
                            <!-- Add more rows as needed -->
                                {{-- @foreach($recentActivity as $activity)
                                    <tr>
                                        <td>{{ $activity->created_at->diffForHumans() }}</td>
                                        <td>{{ $activity->user->name ?? 'System' }}</td>
                                        <td>{{ $activity->description }}</td>
                                    </tr>
                                @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Upcoming Tasks --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Upcoming Tasks</div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead>
                            <tr>
                                <th>Task Title</th>
                                <th>Project</th>
                                <th>Assigned To</th>
                                <th>Due Date</th>
                                <th>Priority</th>
                            </tr>
                            </thead>
                            <tbody>
                            {{-- <tr>
                                <td>Setup DB Schema</td>
                                <td>CRM System</td>
                                <td>Alice</td>
                                <td>June 10, 2025</td>
                                <td><span class="badge bg-danger">High</span></td>
                            </tr>
                            <tr>
                                <td>Final QA</td>
                                <td>HR Portal</td>
                                <td>Bob</td>
                                <td>June 11, 2025</td>
                                <td><span class="badge bg-warning text-dark">Medium</span></td>
                            </tr> --}}
                            <!-- Add more rows as needed -->
                                @foreach($upcomingTasks as $task)
                                    <tr>
                                        <td>{{ $task->title }}</td>
                                        <td>{{ $task->project->title ?? '' }}</td>
                                        <td>{{ $task->user->name ?? '' }}</td>
                                        {{-- <td>{{ $task->due_date->format('M d, Y') }}</td> --}}
                                        <td>{{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($task->priority == 'High') bg-danger
                                                @elseif($task->priority == 'Medium') bg-warning text-dark
                                                @else bg-secondary @endif">
                                                {{ $task->priority }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart.js Scripts --}}
    <script>
        const lineLabels = @json($labels);
        const lineCreated = @json($created);
        const lineCompleted = @json($completed);

        const pieData = [{{ $todo }}, {{ $doing }}, {{ $done }}];

        const barLabels = @json($projects);
        const barData = @json($tasksPerProject);
        console.log(barLabels);
        console.log(barData);


        // Line Chart
        new Chart(document.getElementById('lineChart'), {
            type: 'line',
            data: {
                // labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                labels: lineLabels,
                // datasets: [
                //     { label: 'Created', data: [5, 7, 6, 8, 4, 3, 6], borderColor: '#007bff', fill: false },
                //     { label: 'Completed', data: [3, 6, 5, 7, 2, 2, 5], borderColor: '#28a745', fill: false }
                // ]
                datasets: [
                    { label: 'Created', data: lineCreated, borderColor: '#007bff', fill: false },
                    { label: 'Completed', data: lineCompleted, borderColor: '#28a745', fill: false }
                ]
            },
            options: {
            plugins: {
                legend: {
                    position: 'right' // Move legend to the right side
                }
            }
        }
        });

        // Pie Chart
        new Chart(document.getElementById('pieChart'), {
            type: 'pie',
            data: {
                labels: ['To Do', 'Doing', 'Done'],
                datasets: [{
                    // data: [12, 8, 20],
                    data: pieData,
                    backgroundColor: ['#ffc107', '#17a2b8', '#28a745']
                }]
            }
        });

        // Bar Chart
        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                // labels: ['Alice', 'Bob', 'Charlie', 'Admin'],
                labels: barLabels,
                datasets: [{
                    // data: [10, 15, 8, 12],
                    data: barData,
                    backgroundColor: '#007bff'
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'top' // Move legend to the right side
                    }
                }
            }
        });
    </script>
    </div>
@endsection