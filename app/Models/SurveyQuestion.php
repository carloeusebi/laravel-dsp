<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SurveyQuestion extends Pivot
{
  protected $casts = ['answers' => 'array'];
}
