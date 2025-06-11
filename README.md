# Task Manager

A comprehensive task and project management system built with Laravel. This application allows users to create, manage, and track projects and tasks with features like task assignment, commenting, and progress tracking.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [API Routes](#api-routes)
- [Controllers](#controllers)
- [Migrations](#migrations)
- [Authentication](#authentication)
- [User Roles](#user-roles)

## Features

- **User Authentication**: Secure login and registration system
- **Role-Based Access Control**: Admin and regular user roles
- **Project Management**: Create, edit, and delete projects
- **Task Management**: Create tasks within projects with priorities and deadlines
- **Task Assignment**: Assign tasks to team members
- **Comments**: Add comments to tasks for better collaboration
- **Dashboard**: Overview of projects and tasks
- **Search Functionality**: Search across projects and tasks
- **User Profile**: Update personal information
- **Admin Panel**: User management for administrators

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/dark-parrot/task-management.git
   cd task_manager
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Create and configure the environment file:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure your database in the `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=task_manager
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Run migrations and seed the database:
   ```bash
   php artisan migrate --seed
   ```

6. Start the development server:
   ```bash
   php artisan serve
   ```

## Usage

### User Registration and Login

- Navigate to `/register` to create a new account
- Navigate to `/login` to log in with existing credentials

### Dashboard

- After logging in, you'll be redirected to the dashboard
- View your assigned tasks and projects
- Use the search bar to find specific tasks or projects

### Projects

- Create new projects from the Projects menu
- Assign team members to projects
- Track project status and progress

### Tasks

- Create tasks within projects
- Set priorities, deadlines, and assign to team members
- Add comments to tasks for collaboration
- Track task status and progress

### Admin Panel

- Administrators can access the admin panel at `/admin/dashboard`
- Manage users (create, edit, delete)
- View system-wide statistics

## Project Structure

The application follows the standard Laravel project structure:

```
task_manager/
├── app/                  # Application code
│   ├── Http/             # HTTP layer
│   │   ├── Controllers/  # Controllers
│   │   └── Middleware/   # Middleware
│   └── Models/           # Eloquent models
├── config/               # Configuration files
├── database/             # Database migrations and seeds
├── public/               # Publicly accessible files
├── resources/            # Views and assets
│   ├── css/              # CSS files
│   ├── js/               # JavaScript files
│   └── views/            # Blade templates
├── routes/               # Route definitions
└── storage/              # Application storage
```

## Database Schema

### Users Table
- `id` - Primary key
- `name` - User's full name
- `email` - User's email address (unique)
- `email_verified_at` - Timestamp for email verification
- `password` - Hashed password
- `remember_token` - Token for "remember me" functionality
- `role` - User role (admin or user)
- `created_at` - Timestamp for creation
- `updated_at` - Timestamp for last update

### Projects Table
- `id` - Primary key
- `title` - Project title
- `description` - Project description
- `tags` - Project tags
- `user_id` - Foreign key to users table (project owner)
- `priority` - Project priority (low, medium, high)
- `status` - Project status (todo, in_progress, done, pending, rejected)
- `due_date` - Project deadline
- `estimated_minutes` - Estimated time to complete
- `repeat_cycle` - Repetition cycle for recurring projects
- `created_at` - Timestamp for creation
- `updated_at` - Timestamp for last update

### Tasks Table
- `id` - Primary key
- `user_id` - Foreign key to users table (task assignee)
- `project_id` - Foreign key to projects table
- `title` - Task title
- `description` - Task description
- `tags` - Task tags
- `priority` - Task priority (low, medium, high)
- `status` - Task status (todo, in_progress, done, pending, rejected)
- `due_date` - Task deadline
- `estimated_minutes` - Estimated time to complete
- `repeat_cycle` - Repetition cycle for recurring tasks
- `created_at` - Timestamp for creation
- `updated_at` - Timestamp for last update

### Comments Table
- `id` - Primary key
- `task_id` - Foreign key to tasks table
- `user_id` - Foreign key to users table (comment author)
- `content` - Comment content
- `created_at` - Timestamp for creation
- `updated_at` - Timestamp for last update

### Project Users Table
- `id` - Primary key
- `project_id` - Foreign key to projects table
- `user_id` - Foreign key to users table
- `created_at` - Timestamp for creation
- `updated_at` - Timestamp for last update

## API Routes

### Public Routes
- `GET /` - Home page
- `GET /login` - Login page
- `POST /login` - Process login
- `GET /register` - Registration page
- `POST /register` - Process registration
- `POST /logout` - Logout

### Protected Routes (Require Authentication)
- `GET /dashboard` - User dashboard
- `GET /search` - Search functionality
- `GET /profile` - User profile
- `PUT /profile` - Update user profile

### Project Routes
- `GET /projects` - List all projects
- `GET /projects/create` - Create project form
- `POST /projects` - Store new project
- `GET /projects/{project}` - Show project details
- `GET /projects/{project}/edit` - Edit project form
- `PUT/PATCH /projects/{project}` - Update project
- `DELETE /projects/{project}` - Delete project
- `DELETE /projects/{project}/users/{user}` - Remove user from project

### Task Routes
- `GET /tasks` - List all tasks
- `GET /tasks/create` - Create task form
- `POST /tasks` - Store new task
- `GET /tasks/{task}` - Show task details
- `GET /tasks/{task}/edit` - Edit task form
- `PUT/PATCH /tasks/{task}` - Update task
- `DELETE /tasks/{task}` - Delete task
- `POST /tasks/{task}/comments` - Add comment to task

### Admin Routes
- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/users` - List all users
- `GET /admin/users/create` - Create user form
- `POST /admin/users` - Store new user
- `GET /admin/users/{user}/edit` - Edit user form
- `PUT /admin/users/{user}` - Update user
- `DELETE /admin/users/{user}` - Delete user

## Controllers

### AuthController
Handles user authentication (login, registration, logout).

### UserController
Manages user profiles and user-specific actions.

### UserDashboardController
Handles the user dashboard and search functionality.

### ProjectController
Manages CRUD operations for projects and project-user relationships.

### TaskController
Manages CRUD operations for tasks and task comments.

### AdminController
Handles admin dashboard and user management for administrators.

### Admin\DashboardController
Manages the admin dashboard.

### Admin\UserController
Handles user management from the admin perspective.

## Migrations

The application uses the following migrations to set up the database schema:

1. `0001_01_01_000000_create_users_table.php`
   - Creates the users table
   - Creates the password_reset_tokens table
   - Creates the sessions table

2. `0001_01_01_000001_create_cache_table.php`
   - Creates the cache table for Laravel's cache system

3. `0001_01_01_000002_create_jobs_table.php`
   - Creates the jobs table for Laravel's queue system

4. `2025_06_10_090952_create_projects_table.php`
   - Creates the projects table

5. `2025_06_10_090953_create_tasks_table.php`
   - Creates the tasks table

6. `2025_06_10_090954_create_comments_table.php`
   - Creates the comments table

7. `2025_06_10_090955_create_project_users_table.php`
   - Creates the project_users table for many-to-many relationship

## Authentication

The application uses Laravel's built-in authentication system with some customizations:

- Custom login and registration forms
- Role-based access control
- Remember me functionality
- Password reset capability

## User Roles

The application supports two user roles:

1. **Admin**
   - Full access to all features
   - Can manage users (create, edit, delete)
   - Can access the admin dashboard

2. **User**
   - Can create and manage their own projects
   - Can be assigned to projects by other users
   - Can create and manage tasks within their projects
   - Can comment on tasks# task-management
