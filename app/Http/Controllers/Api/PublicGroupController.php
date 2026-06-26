<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;

// This invokable controller exposes group summary data as JSON.
// It keeps API presentation separate from the Blade web interface.
class PublicGroupController extends Controller
{
    // Called by GET /api/groups/public.
    // It receives an API request, uses the Group model, and returns a JSON
    // list of groups with their task counts.
    public function __invoke()
    {
        // withCount('tasks') asks SQL to count related tasks efficiently.
        // This is better than loading every task just to count them in PHP.
        $groups = Group::withCount('tasks')
            ->orderBy('name')
            ->get()
            // The API response is intentionally small and public-safe.
            ->map(fn (Group $group) => [
                'name' => $group->name,
                'task_count' => $group->tasks_count,
            ]);

        // JSON responses are the normal output format for API routes.
        return response()->json($groups);
    }
}
