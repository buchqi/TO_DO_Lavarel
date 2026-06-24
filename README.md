# KIU Student Task & Activity Management System

## Project Overview

This Laravel project extends the original TODO Task Manager into a university productivity portal for KIU students. Students can register, log in, and manage their own academic tasks, assignments, club activities, personal study items, KIU events, deadlines, tags, and optional file attachments.

The project keeps the original Laravel MVC structure and improves it with authentication, middleware protection, Eloquent relationships, file uploads, a JSON API endpoint, seed data, and a cleaner Bootstrap interface.

## Technologies

- Laravel 12
- PHP 8.2+
- MySQL or SQLite
- Blade
- Bootstrap 5
- Eloquent ORM
- Laravel validation, sessions, and middleware

## Features

- Student registration, login, and logout
- Auth-protected task management routes
- Full task CRUD: create, read, update, delete
- Task status toggle between `pending` and `done`
- Filter tasks by `all`, `pending`, or `done`
- One-to-many relationship: one User has many Tasks
- Many-to-many relationship: Tasks have many Tags, Tags have many Tasks
- Optional file uploads for PDF, JPG, PNG, DOC, and DOCX files
- Attachment replacement and deletion cleanup
- Public JSON API endpoint for sample task data
- KIU-themed Bootstrap Blade UI
- Flash messages and server-side validation errors

## Laravel Concepts Demonstrated

- MVC architecture
- Resource controllers
- Eloquent models and relationships
- One-to-many relationship: `User -> Task`
- Many-to-many relationship: `Task <-> Tag`
- CRUD operations
- Blade layouts, sections, and partials
- Form validation
- Authentication
- Route middleware
- File uploads using Laravel storage
- Database migrations and seeders
- Basic JSON API route

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure your database in `.env`.

Example MySQL settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kiu_task_manager
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations and seed data:

```bash
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

## Seeded Test Account

```text
Email: student@kiu.edu.ge
Password: password
```

## Routes and Pages to Test

- `/login` - Login page
- `/register` - Student registration page
- `/dashboard` - Redirects logged-in users to tasks
- `/tasks` - Auth-protected task list
- `/tasks/create` - Create a task with tags and optional attachment
- `/tasks/{task}/edit` - Edit a task
- `/tasks/{task}/toggle` - Toggle status using a protected POST/PATCH form
- `/api/tasks/public` - Public JSON endpoint with limited task fields

## API Example

```bash
curl http://127.0.0.1:8000/api/tasks/public
```

The endpoint returns limited sample fields:

- `id`
- `title`
- `status`
- `deadline`
- `tags`

## Notes for Presentation

- Guests can only access login/register pages.
- Logged-in students can only see and manage their own tasks.
- Tags are stored separately and connected through the `task_tag` pivot table.
- Uploaded files are stored in `storage/app/public/tasks`.
- Run `php artisan storage:link` so attachment links work from the browser.
