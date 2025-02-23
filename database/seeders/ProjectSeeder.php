<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
       
            Project::create(['name' => 'Project Apollo', 'status' => 'in_progress']);
            Project::create(['name' => 'Project Orion', 'status' => 'completed']);
            Project::create(['name' => 'Project Gemini', 'status' => 'pending']);
    }
}