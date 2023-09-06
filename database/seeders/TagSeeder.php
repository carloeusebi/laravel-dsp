<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csv_file = fopen(base_path('database/seeds/tags.csv'), 'r');

        $fist_line = true;
        while (($data = fgetcsv($csv_file, separator: ',')) !== false) {
            if (!$fist_line) {
                $new_tag = new Tag();

                $new_tag->tag = $data[1];
                $new_tag->color = $data[2];

                $new_tag->save();
            }
            $fist_line = false;
        }
    }
}
