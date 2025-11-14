<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;

class ProgramsSeeder extends Seeder
{
    public function run()
    {
        Program::factory()->count(5)->create();
    }
}
