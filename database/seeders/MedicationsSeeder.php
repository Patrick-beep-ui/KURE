<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medication;

class MedicationsSeeder extends Seeder
{
    public function run()
    {
        Medication::factory()->count(10)->create();
    }
}
