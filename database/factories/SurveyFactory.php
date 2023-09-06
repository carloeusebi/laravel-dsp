<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Question;
use App\Models\Survey;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Survey>
 */
class SurveyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::inRandomOrder()->first()->id,
            'title' => 'test',
            'token' => Survey::generateToken()
        ];
    }


    public function configure()
    {
        return $this->afterCreating(function (Survey $survey) {
            $survey->questions()->attach(Question::inRandomOrder()->limit(rand(4, 15))->get());

            $dice = rand(1, 4); //75% of chances of being dice

            if ($dice > 1) {
                $questions = $survey->questions()->get();
                foreach ($questions as $question) {
                    $answers = $this->generateAnswer($question);
                    $survey->questions()->updateExistingPivot(
                        $question,
                        [
                            'answers' => $answers,
                            'completed' => 1
                        ]
                    );
                }
                $survey->completed = $survey->isCompleted();
                $survey->save();
            }
        });
    }


    /**
     * @param Question $question
     */
    private function generateAnswer(Question $question)
    {
        [$min, $max] = $question->getBoundaries();

        $answers = array_map(function ($item) use ($min, $max) {
            return [
                'id' => $item['id'],
                'answer' => rand($min, $max)
            ];
        }, $question->items);

        return $answers;
    }
}
