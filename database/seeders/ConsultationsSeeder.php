<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Consultation;

class ConsultationsSeeder extends Seeder
{
    public function run()
    {
        Consultation::factory()->count(30)->create();
    }
}
