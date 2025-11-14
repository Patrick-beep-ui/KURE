<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UsersSeeder::class,
            ProgramsSeeder::class,
            StudentsSeeder::class,
            ConditionsSeeder::class,
            StudentConditionsSeeder::class,
            MedicationsSeeder::class,
            InventorySeeder::class,
            ConsultationsSeeder::class,
            ConsultationMedicationsSeeder::class,
            AppointmentsSeeder::class,
        ]);
    }
}
