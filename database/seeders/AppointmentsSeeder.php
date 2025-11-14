<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;

class AppointmentsSeeder extends Seeder
{
    public function run()
    {
        Appointment::factory()->count(25)->create();
    }
}
