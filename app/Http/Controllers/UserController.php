<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        // Get all users using DB query
        $users = DB::table('users')
            ->select('id', 'name', 'email', 'role', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        // Using direct DB query instead of model
        DB::table('users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function edit($id)
    {
        $user = DB::table('users')->find($id);
        
        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'User not found');
        }
        
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = DB::table('users')->find($id);
        
        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'User not found');
        }
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,user',
        ];
        
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'updated_at' => now(),
        ];
        
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        
        DB::table('users')->where('id', $id)->update($updateData);
        
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        // Prevent deleting yourself
        if (Auth::id() == $id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account');
        }
        
        DB::table('users')->where('id', $id)->delete();
        
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }

    public function profile()
    {
        $user = Auth::user();
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
            
        return view('profile', compact('user', 'totalTasks', 'completedTasks', 'pendingTasks', 'projects'));
    }

    public function updateProfile(Request $request)
    {
        $id = Auth::id();
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
        ];
        
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'updated_at' => now(),
        ];
        
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        
        DB::table('users')->where('id', $id)->update($updateData);
        
        return redirect()->route('profile')->with('success', 'Profile updated successfully');
    }
}