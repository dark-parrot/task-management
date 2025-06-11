<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $userRole = DB::table('users')->where('id', $userId)->value('role');
        
        // Start building the query
        $query = DB::table('tasks')
            ->join('users', 'tasks.user_id', '=', 'users.id')
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->select('tasks.*', 'users.name as assigned_to', 'projects.title as project_title');
        
        // Apply user role filter
        if ($userRole !== 'admin') {
            $query->where('tasks.user_id', $userId);
        }
        
        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('tasks.title', 'like', $searchTerm)
                  ->orWhere('tasks.description', 'like', $searchTerm)
                  ->orWhere('tasks.tags', 'like', $searchTerm);
            });
        }
        
        // Apply status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('tasks.status', $request->status);
        }
        
        // Apply priority filter
        if ($request->has('priority') && !empty($request->priority)) {
            $query->where('tasks.priority', $request->priority);
        }
        
        // Apply project filter
        if ($request->has('project_id') && !empty($request->project_id)) {
            $query->where('tasks.project_id', $request->project_id);
        }
        
        // Apply ordering
        $query->orderBy('tasks.due_date');
        
        // Get the results
        $tasks = $query->get();
        
        // Get projects for the filter dropdown
        $projects = DB::table('projects')
            ->select('id', 'title')
            ->orderBy('title')
            ->get();
        
        return view('tasks.index', compact('tasks', 'projects'));
    }

    public function create()
    {
        $userId = Auth::id();
        $userRole = DB::table('users')->where('id', $userId)->value('role');
        
        // Only admin can create tasks
        if ($userRole !== 'admin') {
            return redirect()->route('tasks.index')->with('error', 'You are not authorized to create tasks');
        }
        
        // Admin can assign tasks to any user and any project
        $users = DB::table('users')->select('id', 'name')->get();
        $projects = DB::table('projects')->select('id', 'title')->get();
        
        return view('tasks.create', compact('users', 'projects'));
    }

    public function store(Request $request)
    {
        // Check if the user is authorized to create tasks
        $userId = Auth::id();
        $userRole = DB::table('users')->where('id', $userId)->value('role');
        
        if ($userRole !== 'admin') {
            return redirect()->route('tasks.index')->with('error', 'You are not authorized to create tasks');
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id',
            'tags' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:todo,in_progress,done,pending,rejected',
            'due_date' => 'nullable|date',
            'estimated_minutes' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Insert task
        $taskId = DB::table('tasks')->insertGetId([
            'title' => $request->title,
            'description' => $request->description,
            'project_id' => $request->project_id,
            'user_id' => $request->user_id,
            'tags' => $request->tags,
            'priority' => $request->priority,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'estimated_minutes' => $request->estimated_minutes,
            'repeat_cycle' => $request->repeat_cycle ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully');
    }

    public function show($id)
    {
        $task = DB::table('tasks')
            ->join('users', 'tasks.user_id', '=', 'users.id')
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->select('tasks.*', 'users.name as assigned_to', 'projects.title as project_title')
            ->where('tasks.id', $id)
            ->first();
            
        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Task not found');
        }
        
        // Check if user is authorized to view this task
        $userId = Auth::id();
        $userRole = DB::table('users')->where('id', $userId)->value('role');
        
        if ($userRole !== 'admin' && $task->user_id !== $userId) {
            return redirect()->route('tasks.index')->with('error', 'You are not authorized to view this task');
        }
        
        // Get comments for this task
        $comments = DB::table('comments')
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->where('comments.task_id', $id)
            ->select('comments.*', 'users.name as user_name')
            ->orderBy('comments.created_at', 'desc')
            ->get();
            
        return view('tasks.show', compact('task', 'comments'));
    }

    public function edit($id)
    {
        $task = DB::table('tasks')->find($id);
        
        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Task not found');
        }
        
        // Check if user is authorized to edit this task
        $userId = Auth::id();
        $userRole = DB::table('users')->where('id', $userId)->value('role');
        
        if ($userRole !== 'admin' && $task->user_id !== $userId) {
            return redirect()->route('tasks.index')->with('error', 'You are not authorized to edit this task');
        }
        
        if ($userRole === 'admin') {
            // Admin can assign tasks to any user and any project
            $users = DB::table('users')->select('id', 'name')->get();
            $projects = DB::table('projects')->select('id', 'title')->get();
            
            return view('tasks.edit', compact('task', 'users', 'projects'));
        } else {
            // Regular users can only change status
            return view('tasks.edit', compact('task'));
        }
    }

    public function update(Request $request, $id)
    {
        $task = DB::table('tasks')->find($id);
        
        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Task not found');
        }
        
        // Check if user is authorized to update this task
        $userId = Auth::id();
        $userRole = DB::table('users')->where('id', $userId)->value('role');
        
        if ($userRole !== 'admin' && $task->user_id !== $userId) {
            return redirect()->route('tasks.index')->with('error', 'You are not authorized to update this task');
        }
        
        if ($userRole === 'admin') {
            // Admin can update all fields
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'project_id' => 'required|exists:projects,id',
                'user_id' => 'required|exists:users,id',
                'tags' => 'nullable|string',
                'priority' => 'required|in:low,medium,high',
                'status' => 'required|in:todo,in_progress,done,pending,rejected',
                'due_date' => 'nullable|date',
                'estimated_minutes' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Update task with all fields
            DB::table('tasks')->where('id', $id)->update([
                'title' => $request->title,
                'description' => $request->description,
                'project_id' => $request->project_id,
                'user_id' => $request->user_id,
                'tags' => $request->tags,
                'priority' => $request->priority,
                'status' => $request->status,
                'due_date' => $request->due_date,
                'estimated_minutes' => $request->estimated_minutes,
                'repeat_cycle' => $request->repeat_cycle ?? 0,
                'updated_at' => now(),
            ]);
        } else {
            // Regular users can only update status
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:todo,in_progress,done,pending,rejected',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Update only the status field
            DB::table('tasks')->where('id', $id)->update([
                'status' => $request->status,
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('tasks.show', $id)->with('success', 'Task updated successfully');
    }

    public function destroy($id)
    {
        $task = DB::table('tasks')->find($id);
        
        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Task not found');
        }
        
        // Only admin can delete tasks
        $userId = Auth::id();
        $userRole = DB::table('users')->where('id', $userId)->value('role');
        
        if ($userRole !== 'admin') {
            return redirect()->route('tasks.index')->with('error', 'You are not authorized to delete tasks');
        }
        
        // Delete task and related records
        DB::table('comments')->where('task_id', $id)->delete();
        DB::table('tasks')->where('id', $id)->delete();
        
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully');
    }

    public function addComment(Request $request, $id)
    {
        $task = DB::table('tasks')->find($id);
        
        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Task not found');
        }
        
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Insert comment
        DB::table('comments')->insert([
            'task_id' => $id,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('tasks.show', $id)->with('success', 'Comment added successfully');
    }
}