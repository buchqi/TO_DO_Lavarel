<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // DatabaseSeeder is the entry point for php artisan db:seed.
        // It calls smaller seeders in the correct order so tags exist before
        // tasks try to attach them through the task_tag pivot table.
        $this->call([
            TagSeeder::class,
            TaskSeeder::class,
        ]);
    }
}
