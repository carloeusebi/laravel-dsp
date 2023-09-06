<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\Survey;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class SurveySeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Survey::factory()->count(160)->create();
    }
}
