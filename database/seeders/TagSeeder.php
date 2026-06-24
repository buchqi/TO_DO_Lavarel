<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Exam', 'Assignment', 'Club', 'Personal', 'KIU Event'] as $name) {
            Tag::firstOrCreate(['name' => $name]);
        }
    }
}
