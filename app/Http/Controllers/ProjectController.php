<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $userRole = DB::table('users')->where('id', $userId)->value('role');
        
        // Start building the query
        if ($userRole === 'admin') {
            // Admin can see all projects
            $query = DB::table('projects')
                ->join('users', 'projects.user_id', '=', 'users.id')
                ->select('projects.*', 'users.name as owner_name');
        } else {
            // Regular users can see their own projects and projects they're assigned to
            $query = DB::table('projects')
                ->leftJoin('users', 'projects.user_id', '=', 'users.id')
                ->leftJoin('project_users', 'projects.id', '=', 'project_users.project_id')
                ->where(function($q) use ($userId) {
                    $q->where('projects.user_id', $userId)
                      ->orWhere('project_users.user_id', $userId);
                })
                ->select('projects.*', 'users.name as owner_name')
                ->distinct();
        }
        
        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('projects.title', 'like', $searchTerm)
                  ->orWhere('projects.description', 'like', $searchTerm)
                  ->orWhere('projects.tags', 'like', $searchTerm);
            });
        }
        
        // Apply status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('projects.status', $request->status);
        }
        
        // Apply priority filter
        if ($request->has('priority') && !empty($request->priority)) {
            $query->where('projects.priority', $request->priority);
        }
        
        // Apply sorting
        if ($request->has('sort') && !empty($request->sort)) {
            switch ($request->sort) {
                case 'created_asc':
                    $query->orderBy('projects.created_at', 'asc');
                    break;
                case 'due_date_asc':
                    $query->orderBy('projects.due_date', 'asc');
                    break;
                case 'due_date_desc':
                    $query->orderBy('projects.due_date', 'desc');
                    break;
                case 'created_desc':
                default:
                    $query->orderBy('projects.created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('projects.created_at', 'desc');
        }
        
        // Get the results
        $projects = $query->get();
        
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        // Only admin can create projects
        $userId = Auth::id();
        $userRole = DB::table('users')->where('id', $userId)->value('role');
        
        if ($userRole !== 'admin') {
            return redirect()->route('projects.index')->with('error', 'You are not authorized to create projects');
        }
        
        $users = DB::table('users')->select('id', 'name')->get();
        return view('projects.create', compact('users'));
    }

    public function store(Request $request)
    {
        // Only admin can create projects
        $userId = Auth::id();
        $userRole = DB::table('users')->where('id', $userId)->value('role');
        
        if ($userRole !== 'admin') {
            return redirect()->route('projects.index')->with('error', 'You are not authorized to create projects');
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:todo,in_progress,done,pending,rejected',
            'due_date' => 'nullable|date',
            'estimated_minutes' => 'nullable|integer|min:0',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Insert project
        $projectId = DB::table('projects')->insertGetId([
            'title' => $request->title,
            'description' => $request->description,
            'tags' => $request->tags,
            'user_id' => Auth::id(),
            'priority' => $request->priority,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'estimated_minutes' => $request->estimated_minutes,
            'repeat_cycle' => $request->repeat_cycle ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign team members
        if ($request->has('team_members')) {
            $teamMembers = $request->team_members;
            $projectUsers = [];
            
            foreach ($teamMembers as $userId) {
                $projectUsers[] = [
                    'project_id' => $projectId,
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            if (!empty($projectUsers)) {
                DB::table('project_users')->insert($projectUsers);
            }
        }

        return redirect()->route('projects.index')->with('success', 'Project created successfully');
    }

    public function show($id)
    {
        $project = DB::table('projects')
            ->join('users', 'projects.user_id', '=', 'users.id')
            ->select('projects.*', 'users.name as owner_name')
            ->where('projects.id', $id)
            ->first();
            
        if (!$project) {
            return redirect()->route('projects.index')->with('error', 'Project not found');
        }
        
        // Get team members
        $teamMembers = DB::table('project_users')
            ->join('users', 'project_users.user_id', '=', 'users.id')
            ->where('project_users.project_id', $id)
            ->select('users.id', 'users.name')
            ->get();
            
        // Get tasks for this project
        $tasks = DB::table('tasks')
            ->join('users', 'tasks.user_id', '=', 'users.id')
            ->where('tasks.project_id', $id)
            ->select('tasks.*', 'users.name as assigned_to')
            ->orderBy('tasks.due_date')
            ->get();
            
        return view('projects.show', compact('project', 'teamMembers', 'tasks'));
    }

    public function edit($id)
    {
        $project = DB::table('projects')->find($id);
        
        if (!$project) {
            return redirect()->route('projects.index')->with('error', 'Project not found');
        }
        
        $userId = Auth::id();
        $userRole = DB::table('users')->where('id', $userId)->value('role');
        
        // Check if user is part of this project
        $isProjectMember = DB::table('project_users')
            ->where('project_id', $id)
            ->where('user_id', $userId)
            ->exists();
            
        if ($userRole !== 'admin' && $project->user_id !== $userId && !$isProjectMember) {
            return redirect()->route('projects.index')->with('error', 'You are not authorized to edit this project');
        }
        
        if ($userRole === 'admin') {
            // Admin can edit all fields
            $users = DB::table('users')->select('id', 'name')->get();
            
            // Get current team members
            $teamMembers = DB::table('project_users')
                ->where('project_id', $id)
                ->pluck('user_id')
                ->toArray();
            
            // Get project with users for the table display
            $project->users = DB::table('users')
                ->join('project_users', 'users.id', '=', 'project_users.user_id')
                ->where('project_users.project_id', $id)
                ->select('users.id', 'users.name', 'users.email')
                ->get();
                
            return view('projects.edit', compact('project', 'users', 'teamMembers'));
        } else {
            // Regular users can only change status
            return view('projects.edit', compact('project'));
        }
    }

    public function update(Request $request, $id)
    {
        $project = DB::table('projects')->find($id);
        
        if (!$project) {
            return redirect()->route('projects.index')->with('error', 'Project not found');
        }
        
        $userId = Auth::id();
        $userRole = DB::table('users')->where('id', $userId)->value('role');
        
        // Check if user is part of this project
        $isProjectMember = DB::table('project_users')
            ->where('project_id', $id)
            ->where('user_id', $userId)
            ->exists();
            
        if ($userRole !== 'admin' && $project->user_id !== $userId && !$isProjectMember) {
            return redirect()->route('projects.index')->with('error', 'You are not authorized to update this project');
        }
        
        if ($userRole === 'admin') {
            // Admin can update all fields
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'tags' => 'nullable|string',
                'priority' => 'required|in:low,medium,high',
                'status' => 'required|in:todo,in_progress,done,pending,rejected',
                'due_date' => 'nullable|date',
                'estimated_minutes' => 'nullable|integer|min:0',
                'team_members' => 'nullable|array',
                'team_members.*' => 'exists:users,id',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Update project with all fields
            DB::table('projects')->where('id', $id)->update([
                'title' => $request->title,
                'description' => $request->description,
                'tags' => $request->tags,
                'priority' => $request->priority,
                'status' => $request->status,
                'due_date' => $request->due_date,
                'estimated_minutes' => $request->estimated_minutes,
                'repeat_cycle' => $request->repeat_cycle ?? 0,
                'updated_at' => now(),
            ]);

            // Update team members
            DB::table('project_users')->where('project_id', $id)->delete();
            
            if ($request->has('team_members')) {
                $teamMembers = $request->team_members;
                $projectUsers = [];
                
                foreach ($teamMembers as $memberId) {
                    $projectUsers[] = [
                        'project_id' => $id,
                        'user_id' => $memberId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                if (!empty($projectUsers)) {
                    DB::table('project_users')->insert($projectUsers);
                }
            }
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
            DB::table('projects')->where('id', $id)->update([
                'status' => $request->status,
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('projects.show', $id)->with('success', 'Project updated successfully');
    }

    public function destroy($id)
    {
        $project = DB::table('projects')->find($id);
        
        if (!$project) {
            return redirect()->route('projects.index')->with('error', 'Project not found');
        }
        
        // Only admin can delete projects
        $userId = Auth::id();
        $userRole = DB::table('users')->where('id', $userId)->value('role');
        
        if ($userRole !== 'admin') {
            return redirect()->route('projects.index')->with('error', 'You are not authorized to delete projects');
        }
        
        // Delete project and related records
        DB::table('tasks')->where('project_id', $id)->delete();
        DB::table('project_users')->where('project_id', $id)->delete();
        DB::table('projects')->where('id', $id)->delete();
        
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully');
    }
    
    /**
     * Remove a user from a project
     */
    public function removeUser($projectId, $userId)
    {
        $project = DB::table('projects')->find($projectId);
        
        if (!$project) {
            return redirect()->route('projects.index')->with('error', 'Project not found');
        }
        
        // Only admin can remove users from projects
        $currentUserId = Auth::id();
        $userRole = DB::table('users')->where('id', $currentUserId)->value('role');
        
        if ($userRole !== 'admin') {
            return redirect()->route('projects.show', $projectId)->with('error', 'You are not authorized to modify team members');
        }
        
        // Remove the user from the project
        DB::table('project_users')
            ->where('project_id', $projectId)
            ->where('user_id', $userId)
            ->delete();
        
        return redirect()->route('projects.edit', $projectId)->with('success', 'User removed from project successfully');
    }
}