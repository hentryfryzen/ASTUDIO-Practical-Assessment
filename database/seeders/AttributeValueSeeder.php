<?php

namespace Database\Seeders;

use App\Models\AttributeValue;
use Illuminate\Database\Seeder;

class AttributeValueSeeder extends Seeder
{
    public function run()
    {
        // Project Apollo attributes
        AttributeValue::create(['attribute_id' => 1, 'entity_id' => 1, 'value' => 'IT']);
        AttributeValue::create(['attribute_id' => 2, 'entity_id' => 1, 'value' => '2025-01-10']);
        AttributeValue::create(['attribute_id' => 3, 'entity_id' => 1, 'value' => '2025-05-15']);

        // Project Orion attributes
        AttributeValue::create(['attribute_id' => 1, 'entity_id' => 2, 'value' => 'HR']);
        AttributeValue::create(['attribute_id' => 2, 'entity_id' => 2, 'value' => '2024-11-01']);
        AttributeValue::create(['attribute_id' => 3, 'entity_id' => 2, 'value' => '2025-03-20']);

        // Project Gemini attributes
        AttributeValue::create(['attribute_id' => 1, 'entity_id' => 3, 'value' => 'Finance']);
        AttributeValue::create(['attribute_id' => 2, 'entity_id' => 3, 'value' => '2025-02-01']);
        AttributeValue::create(['attribute_id' => 3, 'entity_id' => 3, 'value' => '2025-06-30']);
    }
}
