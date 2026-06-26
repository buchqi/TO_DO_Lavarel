<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;

// This invokable controller handles one API endpoint.
// Laravel calls __invoke() when the route points to the class name instead
// of a specific method, which is useful for small single-purpose controllers.
class PublicTaskController extends Controller
{
    // Called by GET /api/tasks/public.
    // It receives an API request, uses the Task model and its tags relationship,
    // and returns JSON instead of a Blade page.
    public function __invoke()
    {
        // with('tags') eager-loads the many-to-many relationship so the map
        // below can read tag names without running one query per task.
        $tasks = Task::with('tags')
            ->latest()
            ->limit(10)
            ->get()
            // map() transforms Eloquent models into a public response shape.
            // This avoids exposing every database column to API consumers.
            ->map(fn (Task $task) => [
                'id' => $task->id,
                'title' => $task->title,
                'status' => $task->status,
                'deadline' => optional($task->deadline)->toDateString(),
                'tags' => $task->tags->pluck('name')->values(),
            ]);

        // response()->json() sets the correct JSON headers and serializes
        // the array/collection for API clients.
        return response()->json($tasks);
    }
}
