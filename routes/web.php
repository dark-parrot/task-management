<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserDashboardController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    // User dashboard
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    
    // Global search
    Route::get('/search', [UserDashboardController::class, 'search'])->name('search');
    
    // User profile
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    
    // Projects
    Route::resource('projects', ProjectController::class);
    Route::delete('/projects/{project}/users/{user}', [ProjectController::class, 'removeUser'])->name('projects.remove-user');
    
    // Tasks
    Route::resource('tasks', TaskController::class);
    Route::post('/tasks/{task}/comments', [TaskController::class, 'addComment'])->name('tasks.comments.store');
    
    // Admin routes
    Route::get('/admin/dashboard', [AdminController::class, 'index'])
        ->middleware(['auth', 'admin'])
        ->name('admin.dashboard');
        
    // User management
    Route::get('/admin/users', [AdminController::class, 'users'])
        ->middleware(['auth', 'admin'])
        ->name('admin.users.index');
    Route::get('/admin/users/create', [AdminController::class, 'createUser'])
        ->middleware(['auth', 'admin'])
        ->name('admin.users.create');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])
        ->middleware(['auth', 'admin'])
        ->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [AdminController::class, 'editUser'])
        ->middleware(['auth', 'admin'])
        ->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])
        ->middleware(['auth', 'admin'])
        ->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])
        ->middleware(['auth', 'admin'])
        ->name('admin.users.destroy');
});

// Admin layout preview (for development)
Route::get('/admin_layout', function () {
    return view('layout.admin_layout');
});

// Test admin dashboard route without middleware
Route::get('/admin_test', function() {
    return "Admin Test Route";
});