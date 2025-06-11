<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
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
        
        return view('dashboard', compact(
            'totalTasks', 'completedTasks', 'pendingTasks', 'projects',
            'upcomingTasks', 'recentTasks',
            'todo', 'inProgress', 'done', 'pending',
            'labels', 'created', 'completed'
        ));
    }
    
    public function search(Request $request)
    {
        $userId = Auth::id();
        $userRole = DB::table('users')->where('id', $userId)->value('role');
        $searchTerm = $request->input('q');
        
        if (empty($searchTerm)) {
            return redirect()->route('dashboard');
        }
        
        $searchTermLike = '%' . $searchTerm . '%';
        
        // Search tasks
        if ($userRole === 'admin') {
            // Admin can see all tasks
            $tasks = DB::table('tasks')
                ->join('users', 'tasks.user_id', '=', 'users.id')
                ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
                ->where(function($query) use ($searchTermLike) {
                    $query->where('tasks.title', 'like', $searchTermLike)
                          ->orWhere('tasks.description', 'like', $searchTermLike)
                          ->orWhere('tasks.tags', 'like', $searchTermLike);
                })
                ->select('tasks.*', 'users.name as assigned_to', 'projects.title as project_title')
                ->orderBy('tasks.due_date')
                ->get();
        } else {
            // Regular users can see their own tasks
            $tasks = DB::table('tasks')
                ->join('users', 'tasks.user_id', '=', 'users.id')
                ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
                ->where('tasks.user_id', $userId)
                ->where(function($query) use ($searchTermLike) {
                    $query->where('tasks.title', 'like', $searchTermLike)
                          ->orWhere('tasks.description', 'like', $searchTermLike)
                          ->orWhere('tasks.tags', 'like', $searchTermLike);
                })
                ->select('tasks.*', 'users.name as assigned_to', 'projects.title as project_title')
                ->orderBy('tasks.due_date')
                ->get();
        }
        
        // Search projects
        if ($userRole === 'admin') {
            // Admin can see all projects
            $projects = DB::table('projects')
                ->join('users', 'projects.user_id', '=', 'users.id')
                ->where(function($query) use ($searchTermLike) {
                    $query->where('projects.title', 'like', $searchTermLike)
                          ->orWhere('projects.description', 'like', $searchTermLike)
                          ->orWhere('projects.tags', 'like', $searchTermLike);
                })
                ->select('projects.*', 'users.name as owner_name')
                ->orderBy('projects.created_at', 'desc')
                ->get();
        } else {
            // Regular users can see their own projects and projects they're assigned to
            $projects = DB::table('projects')
                ->leftJoin('users', 'projects.user_id', '=', 'users.id')
                ->leftJoin('project_users', 'projects.id', '=', 'project_users.project_id')
                ->where(function($query) use ($userId) {
                    $query->where('projects.user_id', $userId)
                          ->orWhere('project_users.user_id', $userId);
                })
                ->where(function($query) use ($searchTermLike) {
                    $query->where('projects.title', 'like', $searchTermLike)
                          ->orWhere('projects.description', 'like', $searchTermLike)
                          ->orWhere('projects.tags', 'like', $searchTermLike);
                })
                ->select('projects.*', 'users.name as owner_name')
                ->distinct()
                ->orderBy('projects.created_at', 'desc')
                ->get();
        }
        
        return view('search_results', compact('tasks', 'projects', 'searchTerm'));
    }
}