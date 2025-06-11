<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        // Get user's tasks statistics
        $totalTasks = DB::table('tasks')->where('user_id', $userId)->count();
        $completedTasks = DB::table('tasks')->where('user_id', $userId)->where('status', 'done')->count();
        $pendingTasks = DB::table('tasks')->where('user_id', $userId)->where('status', '!=', 'done')->count();
        
        // Get user's projects
        $projects = DB::table('projects')
            ->leftJoin('project_users', 'projects.id', '=', 'project_users.project_id')
            ->where(function($query) use ($userId) {
                $query->where('projects.user_id', $userId)
                      ->orWhere('project_users.user_id', $userId);
            })
            ->select('projects.*')
            ->distinct()
            ->count();
        
        // Get upcoming tasks (due in next 7 days)
        $upcomingTasks = DB::table('tasks')
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->where('tasks.user_id', $userId)
            ->where('tasks.status', '!=', 'done')
            ->whereDate('tasks.due_date', '>=', now())
            ->whereDate('tasks.due_date', '<=', now()->addDays(7))
            ->select('tasks.*', 'projects.title as project_title')
            ->orderBy('tasks.due_date')
            ->get();
        
        // Get recent activities (last 5 tasks updated)
        $recentTasks = DB::table('tasks')
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->where('tasks.user_id', $userId)
            ->select('tasks.*', 'projects.title as project_title')
            ->orderBy('tasks.updated_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get task status breakdown
        $todo = DB::table('tasks')->where('user_id', $userId)->where('status', 'todo')->count();
        $inProgress = DB::table('tasks')->where('user_id', $userId)->where('status', 'in_progress')->count();
        $done = DB::table('tasks')->where('user_id', $userId)->where('status', 'done')->count();
        $pending = DB::table('tasks')->where('user_id', $userId)->where('status', 'pending')->count();
        
        // Get tasks created per day (last 7 days)
        $labels = [];
        $created = [];
        $completed = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('D');
            $created[] = DB::table('tasks')
                ->where('user_id', $userId)
                ->whereDate('created_at', $date)
                ->count();
            $completed[] = DB::table('tasks')
                ->where('user_id', $userId)
                ->where('status', 'done')
                ->whereDate('updated_at', $date)
                ->count();
        }
        
        // Rename variables to match the admin_dashboard view
        $totalProjects = DB::table('projects')->count();
        $activeTasks = $pendingTasks;
        $registeredUsers = DB::table('users')->count();
        $doing = $inProgress;
        
        // Get projects for bar chart
        $projectsForChart = DB::table('projects')->where('status', 'in_progress')->get();
        $tasksPerProject = [];
        foreach ($projectsForChart as $project) {
            $tasksPerProject[] = DB::table('tasks')
                ->where('project_id', $project->id)
                ->count();
        }
        
        return view('admin_dashboard', compact(
            'totalProjects', 'completedTasks', 'activeTasks', 'registeredUsers',
            'upcomingTasks', 
            'todo', 'doing', 'done',
            'labels', 'created', 'completed',
            'projectsForChart', 'tasksPerProject'
        ));
    }
}