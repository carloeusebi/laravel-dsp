<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Patient;
use Faker\Factory;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $NUMBER_OF_PATIENTS = 80;
        $faker = Factory::create('it_IT');

        for ($i = 0; $i < $NUMBER_OF_PATIENTS; $i++) {
            $new_patient = new Patient();

            $sex = $faker->boolean() ? 'M' : 'F';
            $birthday = $faker->dateTimeBetween('-70 years', '-18 years');

            $new_patient->fname = $sex === 'M' ? $faker->firstNameMale() : $faker->firstNameFemale();
            $new_patient->lname = $faker->lastName();
            $new_patient->sex = $sex;
            $new_patient->birthday = $birthday;
            $new_patient->birthplace = $faker->city();
            $new_patient->address = $faker->address();
            $new_patient->begin = $faker->dateTimeBetween('-4 years');
            $new_patient->email = $faker->email();
            $new_patient->phone = $faker->phoneNumber();
            $new_patient->codice_fiscale = $faker->taxId();
            $new_patient->weight = $faker->numberBetween(50, 110);
            $new_patient->height = $faker->numberBetween(150, 200);
            $new_patient->job = $faker->jobTitle();

            $new_patient->save();
        }
    }
}
