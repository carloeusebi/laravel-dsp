<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['question', 'description', 'type', 'items', 'legend', 'variables', 'not_current'];

    protected $casts = [
        'items' => 'array',
        'legend' => 'array',
        'variables' => 'array',
    ];

    static function labels(): array
    {
        return
            [
                'id' => 'id',
                'question' => 'Nome del questionario',
                'description' => 'Descrizione',
                'type' => 'Tipo',
                'items' => 'Lista delle domande',
                'legend' => 'Legenda',
                'variables' => 'Variabili',
            ];
    }


    /**
     * Binds the Answers value (which are stored in the pivot table) inside the Questionnaire's item as 'answer' key.
     */
    public function bindAnswers()
    {
        $answers = $this->pivot->answers ?? [];

        $this->items = array_map(function ($item) use ($answers) {
            if ($answers) {
                $key = array_search($item['id'], array_column($answers, 'id'));
                if ($key !== false) { // not (!$key) because $key could be 0!
                    $item['answer'] = $answers[$key]['answer'];
                }
            }
            return $item;
        }, $this->items);
    }


    /**
     * Checks if the Questionnaire's is used in Surveys;
     */
    public function isInSurveys(): bool
    {
        return $this->surveys()->count() > 0;
    }


    /**
     * The tags that belongs the Questionnaire
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }


    /**
     * Returns the minimum and maximum values for the Questionnaire's items.
     * @return array [min, max]
     */
    public function getBoundaries(): array
    {
        $min = $max = 0;
        switch ($this->type) {
            case 'MUL':
                $points = [];
                foreach ($this->items[0]['multipleAnswers'] as $answer) $points[] = $answer['points'];
                $min = min($points);
                $max = max($points);
                break;
            case 'EDI':
                $min = 0;
                $max = 5;
                break;
            default:
                $min = intval(substr($this->type, 0, 1));
                $max = intval(substr($this->type, -1));
        }
        return [$min, $max];
    }


    /**
     * The Surveys that contain the Question
     */
    public function surveys()
    {
        return $this->belongsToMany(Survey::class);
    }
}
