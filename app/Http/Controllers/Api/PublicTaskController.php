<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;

class PublicTaskController extends Controller
{
    public function __invoke()
    {
        $tasks = Task::with('tags')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn (Task $task) => [
                'id' => $task->id,
                'title' => $task->title,
                'status' => $task->status,
                'deadline' => optional($task->deadline)->toDateString(),
                'tags' => $task->tags->pluck('name')->values(),
            ]);

        return response()->json($tasks);
    }
}
