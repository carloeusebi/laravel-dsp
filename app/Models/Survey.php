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
     * Checks if survey is completed
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
