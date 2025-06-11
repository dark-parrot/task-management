<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // KPIs
        $totalProjects = DB::table('projects')->count();
        $completedTasks = DB::table('tasks')->where('status', 'done')->count();
        $activeTasks = DB::table('tasks')->where('status', '!=', 'done')->count();
        $registeredUsers = DB::table('users')->count();

        // Line Chart Data (last 7 days)
        $labels = [];
        $created = [];
        $completed = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('D');
            $created[] = DB::table('tasks')->whereDate('created_at', $date)->count();
            $completed[] = DB::table('tasks')->where('status', 'done')->whereDate('updated_at', $date)->count();
        }

        // Pie Chart Data
        $todo = DB::table('tasks')->where('status', 'todo')->count();
        $doing = DB::table('tasks')->where('status', 'in_progress')->count();
        $done = DB::table('tasks')->where('status', 'done')->count();

        // Bar Chart Data (tasks per project)
        $projectsData = DB::table('projects')->where('status', 'in_progress')->get();
        $projects = $projectsData->pluck('title');
        $tasksPerProject = [];
        foreach ($projectsData as $project) {
            $tasksPerProject[] = DB::table('tasks')
                ->where('project_id', $project->id)
                ->count();
        }

        // Upcoming Tasks (due in next 7 days)
        $upcomingTasks = DB::table('tasks')
            ->whereDate('due_date', '>=', now())
            ->whereDate('due_date', '<=', now()->addDays(7))
            ->orderBy('due_date')
            ->get();

        return view('admin.dashboard', compact(
            'totalProjects', 'completedTasks', 'activeTasks', 'registeredUsers',
            'labels', 'created', 'completed',
            'todo', 'doing', 'done',
            'projects', 'tasksPerProject',
            'upcomingTasks'
        ));
    }
}