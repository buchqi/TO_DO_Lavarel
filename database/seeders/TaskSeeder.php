<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            [
                'title' => 'Finish Laravel TODO assignment report',
                'description' => 'Write project overview, MVC design choices, and screenshots.',
                'status' => 'pending',
                'deadline' => now()->addDays(2)->toDateString(),
            ],
            [
                'title' => 'Prepare slides for presentation',
                'description' => 'Keep slides short and focus on CRUD features.',
                'status' => 'done',
                'deadline' => now()->addDay()->toDateString(),
            ],
            [
                'title' => 'Review validation and error messages',
                'description' => 'Double-check required fields and friendly errors.',
                'status' => 'pending',
                'deadline' => now()->addDays(3)->toDateString(),
            ],
            [
                'title' => 'Test update and delete flow',
                'description' => 'Verify edit form, status buttons, and delete confirmation.',
                'status' => 'done',
                'deadline' => now()->toDateString(),
            ],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}
