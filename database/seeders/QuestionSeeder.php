<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csv_file = fopen(base_path('database/seeds/questions.csv'), 'r');

        $first_line = true;
        while (($data = fgetcsv($csv_file, separator: ',')) !== false) {
            if (!$first_line) {
                $question = new Question();

                $question->question = $data[1];
                $question->description = $data[2];
                $question->type = $data[3];
                $question->items = json_decode($data[4], true);
                $question->legend = json_decode($data[5], true);
                $question->variables = json_decode($data[6], true);

                $question->save();
            }
            $first_line = false;
        }
    }
}
