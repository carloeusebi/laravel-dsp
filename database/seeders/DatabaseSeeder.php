<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([UserSeeder::class, PatientSeeder::class, TagSeeder::class, QuestionSeeder::class, QuestionTagSeeder::class, SurveySeeder::class]);
    }
}
