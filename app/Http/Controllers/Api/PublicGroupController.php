<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;

class PublicGroupController extends Controller
{
    public function __invoke()
    {
        $groups = Group::withCount('tasks')
            ->orderBy('name')
            ->get()
            ->map(fn (Group $group) => [
                'name' => $group->name,
                'task_count' => $group->tasks_count,
            ]);

        return response()->json($groups);
    }
}
