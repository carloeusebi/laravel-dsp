<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'patient_id', 'token'];


    /**
     * Sets the completed attribute. Checks if within the survey's questions there is none that is not completed.
     * Checks the completed column in the pivot question_survey table.
     */
    public function isCompleted(): bool
    {
        return $this->questions()->withPivotValue('completed', 0)->get()->isEmpty();
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class)
            ->withPivot(['answers', 'completed'])
            ->using(SurveyQuestion::class);
    }

    static function generateToken(): string
    {
        return bin2hex(random_bytes(16));
    }
}
