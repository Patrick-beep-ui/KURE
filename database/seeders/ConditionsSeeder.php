<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Condition;

class ConditionsSeeder extends Seeder
{
    public function run()
    {
        Condition::factory()->count(10)->create();
    }
}
