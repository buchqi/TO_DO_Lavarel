<?php

namespace Database\Seeders;

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
        $user = User::firstOrCreate(
            ['email' => 'student@kiu.edu.ge'],
            [
                'name' => 'KIU Student',
                'password' => 'password',
            ]
        );

        $tasks = [
            [
                'title' => 'Finish Laravel TODO assignment report',
                'description' => 'Write project overview, MVC design choices, and screenshots.',
                'status' => 'pending',
                'deadline' => now()->addDays(2)->toDateString(),
                'tags' => ['Assignment'],
            ],
            [
                'title' => 'Prepare slides for presentation',
                'description' => 'Keep slides short and focus on CRUD features.',
                'status' => 'done',
                'deadline' => now()->addDay()->toDateString(),
                'tags' => ['Assignment', 'Exam'],
            ],
            [
                'title' => 'Attend KIU student club meeting',
                'description' => 'Check activity plan and volunteer schedule.',
                'status' => 'pending',
                'deadline' => now()->addDays(3)->toDateString(),
                'tags' => ['Club', 'KIU Event'],
            ],
            [
                'title' => 'Organize personal study plan',
                'description' => 'Reserve time for database revision and project polishing.',
                'status' => 'done',
                'deadline' => now()->toDateString(),
                'tags' => ['Personal'],
            ],
        ];

        foreach ($tasks as $task) {
            $tagNames = $task['tags'];
            unset($task['tags']);

            $createdTask = Task::create([
                ...$task,
                'user_id' => $user->id,
            ]);

            $createdTask->tags()->sync(Tag::whereIn('name', $tagNames)->pluck('id'));
        }
    }
}
