# KIU Student Task & Activity Management System

## Project Overview

This Laravel project extends the original TODO Task Manager into a university productivity portal for KIU students. Students can register, log in, manage personal academic tasks, create groups, add team members, and share tasks for assignments, clubs, KIU events, deadlines, tags, and optional file attachments.

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
- Group/team CRUD for shared work
- Add and remove group members by email
- Personal tasks when `group_id` is empty
- Shared group tasks when `group_id` is set
- Task status toggle between `pending` and `done`
- Filter tasks by `all`, `pending`, or `done`
- One-to-many relationship: one User has many Tasks
- One-to-many relationship: one User owns many Groups
- Many-to-many relationship: Tasks have many Tags, Tags have many Tasks
- Many-to-many relationship: Users belong to many Groups through `group_user`
- One-to-many relationship: one Group has many shared Tasks
- Optional file uploads for PDF, JPG, PNG, DOC, and DOCX files
- Attachment replacement and deletion cleanup
- Public JSON API endpoint for sample task data
- Public JSON API endpoint for group names and task counts
- KIU-themed Bootstrap Blade UI
- Flash messages and server-side validation errors

## Laravel Concepts Demonstrated

- MVC architecture
- Resource controllers
- Eloquent models and relationships
- One-to-many relationship: `User -> Task`
- One-to-many relationship: `User -> ownedGroups`
- One-to-many relationship: `Group -> Task`
- Nullable relationship: `Task -> Group`
- Many-to-many relationship: `Task <-> Tag`
- Many-to-many relationship: `User <-> Group`
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

More seeded users:

```text
Email: teammate@kiu.edu.ge
Password: password

Email: club@kiu.edu.ge
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
- `/groups` - View groups you own or belong to
- `/groups/create` - Create a group
- `/groups/{group}` - View group details, members, and shared tasks
- `/groups/{group}/edit` - Edit an owned group
- `/groups/{group}/members` - Add a member by email
- `/api/tasks/public` - Public JSON endpoint with limited task fields
- `/api/groups/public` - Public JSON endpoint with group names and task counts

## API Example

```bash
curl http://127.0.0.1:8000/api/tasks/public
curl http://127.0.0.1:8000/api/groups/public
```

The endpoint returns limited sample fields:

- `id`
- `title`
- `status`
- `deadline`
- `tags`

The group endpoint returns:

- `name`
- `task_count`

## Group Authorization Rules

- Only authenticated users can access group pages.
- Users can view groups they own or belong to.
- Only the owner can edit or delete a group.
- Only the owner can add or remove members.
- Members cannot be added twice.
- Group owners are not added again as members.
- Personal tasks are visible only to their creator.
- Shared tasks are visible to the group owner and group members.
- Shared tasks can be edited or deleted only by the task creator or the group owner.
- Users cannot assign a task to a group they do not own or belong to.

## Notes for Presentation

- Guests can only access login/register pages and public API endpoints.
- Logged-in students can see personal tasks and shared tasks from their groups.
- Tags are stored separately and connected through the `task_tag` pivot table.
- Group memberships are stored in the `group_user` pivot table with a simple `role` field.
- Uploaded files are stored in `storage/app/public/tasks`.
- Run `php artisan storage:link` so attachment links work from the browser.
