<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // In Laravel 11, middleware is applied in routes, not in controllers
    
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
    
    public function users(Request $request)
    {
        // Start building the query
        $query = DB::table('users')
            ->select('id', 'name', 'email', 'role', 'created_at');
        
        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm);
            });
        }
        
        // Get the results ordered by creation date
        $users = $query->orderBy('created_at', 'desc')->get();
        
        return view('admin.users.index', compact('users'));
    }
    
    public function createUser()
    {
        return view('admin.users.create');
    }
    
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        // Using direct DB query instead of model
        DB::table('users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully');
    }
    
    public function editUser($id)
    {
        $user = DB::table('users')->find($id);
        
        if (!$user) {
            return redirect()->route('admin.users')->with('error', 'User not found');
        }
        
        return view('admin.users.edit', compact('user'));
    }
    
    public function updateUser(Request $request, $id)
    {
        $user = DB::table('users')->find($id);
        
        if (!$user) {
            return redirect()->route('admin.users')->with('error', 'User not found');
        }
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,user',
        ];
        
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }
        
        $request->validate($rules);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'updated_at' => now(),
        ];
        
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }
        
        DB::table('users')->where('id', $id)->update($updateData);
        
        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }
    
    public function destroyUser($id)
    {
        // Prevent deleting yourself
        if (auth()->id() == $id) {
            return redirect()->route('admin.users')->with('error', 'You cannot delete your own account');
        }
        
        DB::table('users')->where('id', $id)->delete();
        
        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }
}