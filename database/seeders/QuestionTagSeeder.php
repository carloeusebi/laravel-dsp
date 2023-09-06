<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csv_file = fopen(base_path('database/seeds/question_tag.csv'), 'r');

        $first_line = true;
        while (($data = fgetcsv($csv_file, separator: ',')) !== false) {
            if (!$first_line) {
                $question_id = $data[0];
                $tag_id = $data[1];

                DB::table('question_tag')->insert(['question_id' => $question_id, 'tag_id' => $tag_id]);
            }
            $first_line = false;
        }
    }
}
