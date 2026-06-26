<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Task;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seeders create realistic data for development and demonstrations.
        // These users allow authentication, group ownership, and membership
        // scenarios to be tested without manual database entry.
        $student = User::firstOrCreate(
            ['email' => 'student@kiu.edu.ge'],
            [
                'name' => 'KIU Student',
                'password' => 'password',
            ]
        );

        $teammate = User::firstOrCreate(
            ['email' => 'teammate@kiu.edu.ge'],
            [
                'name' => 'Project Teammate',
                'password' => 'password',
            ]
        );

        $clubMember = User::firstOrCreate(
            ['email' => 'club@kiu.edu.ge'],
            [
                'name' => 'Club Member',
                'password' => 'password',
            ]
        );

        // Groups are created with owner_id so the authorization rules can be
        // demonstrated: owners manage groups, members collaborate inside them.
        $projectGroup = Group::firstOrCreate(
            ['name' => 'Laravel Project Team', 'owner_id' => $student->id],
            ['description' => 'Team for the KIU Laravel final project.']
        );

        $clubGroup = Group::firstOrCreate(
            ['name' => 'KIU Student Club', 'owner_id' => $teammate->id],
            ['description' => 'Planning student club activities and events.']
        );

        // syncWithoutDetaching adds many-to-many membership rows without
        // removing existing members. The array values become pivot data.
        $projectGroup->users()->syncWithoutDetaching([
            $teammate->id => ['role' => 'member'],
            $clubMember->id => ['role' => 'member'],
        ]);

        $clubGroup->users()->syncWithoutDetaching([
            $student->id => ['role' => 'member'],
            $clubMember->id => ['role' => 'member'],
        ]);

        // The task array mixes personal tasks and group tasks so the UI can
        // show both ownership paths during demos.
        $tasks = [
            [
                'title' => 'Finish Laravel TODO assignment report',
                'description' => 'Write project overview, MVC design choices, and screenshots.',
                'status' => 'pending',
                'deadline' => now()->addDays(2)->toDateString(),
                'tags' => ['Assignment'],
                'user_id' => $student->id,
                'group_id' => null,
            ],
            [
                'title' => 'Prepare slides for presentation',
                'description' => 'Keep slides short and focus on CRUD features.',
                'status' => 'done',
                'deadline' => now()->addDay()->toDateString(),
                'tags' => ['Assignment', 'Exam'],
                'user_id' => $student->id,
                'group_id' => $projectGroup->id,
            ],
            [
                'title' => 'Attend KIU student club meeting',
                'description' => 'Check activity plan and volunteer schedule.',
                'status' => 'pending',
                'deadline' => now()->addDays(3)->toDateString(),
                'tags' => ['Club', 'KIU Event'],
                'user_id' => $teammate->id,
                'group_id' => $clubGroup->id,
            ],
            [
                'title' => 'Organize personal study plan',
                'description' => 'Reserve time for database revision and project polishing.',
                'status' => 'done',
                'deadline' => now()->toDateString(),
                'tags' => ['Personal'],
                'user_id' => $student->id,
                'group_id' => null,
            ],
            [
                'title' => 'Review shared task permissions',
                'description' => 'Confirm only creators and group owners can edit shared tasks.',
                'status' => 'pending',
                'deadline' => now()->addDays(4)->toDateString(),
                'tags' => ['Assignment'],
                'user_id' => $teammate->id,
                'group_id' => $projectGroup->id,
            ],
        ];

        // Each task is created first, then its tag names are translated into
        // tag ids and written to the task_tag pivot table with sync().
        foreach ($tasks as $task) {
            $tagNames = $task['tags'];
            unset($task['tags']);

            $createdTask = Task::create([
                ...$task,
            ]);

            $createdTask->tags()->sync(Tag::whereIn('name', $tagNames)->pluck('id'));
        }
    }
}
