<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        Project::create(['name' => 'ProjectA', 'status' => 'in_progress']);
        Project::create(['name' => 'ProjectB', 'status' => 'completed']);
    }
}