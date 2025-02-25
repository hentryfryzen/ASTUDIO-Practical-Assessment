<?php

namespace Database\Seeders;

use App\Models\AttributeValue;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Attribute;

class AttributeValueSeeder extends Seeder
{
    public function run()
    { 
         $projectA = Project::where('name', 'ProjectA')->first();
        $projectB = Project::where('name', 'ProjectB')->first();

        $department = Attribute::where('name', 'department')->first();
        $startDate = Attribute::where('name', 'start_date')->first();
        $endDate = Attribute::where('name', 'end_date')->first();

        // Project A Attributes
        AttributeValue::create(['attribute_id' => $department->id, 'entity_id' => $projectA->id, 'value' => 'IT']);
        AttributeValue::create(['attribute_id' => $startDate->id, 'entity_id' => $projectA->id, 'value' => '2024-01-01']);
        AttributeValue::create(['attribute_id' => $endDate->id, 'entity_id' => $projectA->id, 'value' => '2024-12-31']);

        // Project B Attributes
        AttributeValue::create(['attribute_id' => $department->id, 'entity_id' => $projectB->id, 'value' => 'HR']);
        AttributeValue::create(['attribute_id' => $startDate->id, 'entity_id' => $projectB->id, 'value' => '2024-02-01']);
        AttributeValue::create(['attribute_id' => $endDate->id, 'entity_id' => $projectB->id, 'value' => '2024-11-30']);
    }
}
