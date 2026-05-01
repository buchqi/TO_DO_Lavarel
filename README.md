# Laravel TODO Task Manager

## Project Overview
This project is a Laravel-based task management web application developed for a university midterm project.  
It demonstrates proper Laravel MVC architecture, CRUD functionality, database migrations, Eloquent ORM, Blade templating, and Bootstrap UI design.

The application allows users to:
- Create tasks
- Read tasks
- Update tasks
- Delete tasks
- Mark tasks as `Pending` or `Done`
- Filter tasks by status (`All`, `Pending`, `Done`)

## Technologies Used
- Laravel
- PHP
- MySQL
- Blade
- Bootstrap
- Eloquent ORM
- XAMPP

## Installation Instructions
Follow these steps to run the project locally.

1. Clone the repository
```bash
git clone <your-repository-url>
cd todo-task-manager
```

2. Install PHP dependencies
```bash
composer install
```

3. Configure environment file
```bash
cp .env.example .env
php artisan key:generate
```

4. Create a MySQL database
- Open phpMyAdmin (XAMPP) or MySQL client.
- Create a new database (example: `todo_task_manager`).
- Update database credentials in `.env`:
  - `DB_DATABASE`
  - `DB_USERNAME`
  - `DB_PASSWORD`

5. Run migrations
```bash
php artisan migrate
```

6. Seed sample data
```bash
php artisan db:seed
```

7. Start the development server
```bash
php artisan serve
```

Open in browser: `http://127.0.0.1:8000/tasks`

### Quick Command List
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

## Seeder Information
To populate the database with sample tasks for testing, run:

```bash
php artisan db:seed
```

This project includes `TaskSeeder` with sample `pending` and `done` tasks.

## Project Structure
The project follows Laravel MVC structure:

- **Models**:  
  `app/Models/Task.php`  
  Handles task data and Eloquent configuration (`$fillable`, `$casts`).

- **Controllers**:  
  `app/Http/Controllers/TaskController.php`  
  Handles business logic for CRUD, status updates, and filtering.

- **Migrations**:  
  `database/migrations/*create_tasks_table.php`  
  Defines database schema for tasks (`title`, `description`, `status`, `deadline`).

- **Blade Views**:  
  `resources/views/layouts/app.blade.php`  
  `resources/views/tasks/index.blade.php`  
  `resources/views/tasks/create.blade.php`  
  `resources/views/tasks/edit.blade.php`  
  Provides layout and UI for listing, creating, and editing tasks.

## Features
- Full task CRUD operations:
  - Create new task
  - View all tasks
  - Edit existing task
  - Delete task with confirmation
- Status management:
  - Mark task as `Pending` or `Done`
- Task filtering:
  - Filter by `All`, `Pending`, or `Done`
- Validation:
  - Server-side validation with user-friendly error messages
- Flash messaging:
  - Success messages after create/update/delete/status actions
- UI:
  - Clean Bootstrap layout using Blade `@extends` and `@section`

## Future Improvements
- Authentication (Login/Register)
- Search functionality
- Pagination
- Enhanced AJAX interactions (for smoother filtering and partial UI updates)

## Academic Note
This project was created as a structured foundation for future Laravel coursework and the final project.  
The codebase is organized to support extension with advanced features in later phases.

