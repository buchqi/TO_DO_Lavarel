<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        // Tags are seeded once and reused by tasks.
        // firstOrCreate makes the seeder safe to run multiple times because it
        // will not create duplicate rows for the same tag name.
        foreach (['Exam', 'Assignment', 'Club', 'Personal', 'KIU Event'] as $name) {
            Tag::firstOrCreate(['name' => $name]);
        }
    }
}
