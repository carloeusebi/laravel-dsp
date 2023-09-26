<?php

namespace App\Helpers;

use App\Models\Patient;
use App\Models\Question;
use App\Models\Survey;
use Exception;

class Results
{

  private Survey $survey;
  private Patient $patient;
  private Question $activeQuestion;

  public function __construct(Survey $survey)
  {
    $this->survey = $survey;
    if ($survey->patient)
      $this->patient = $survey->patient;
    return $this;
  }

  /**
   * Calculate scores for the given Survey.
   * 
   */
  public function calculate()
  {
    $survey = $this->survey;
    foreach ($survey->questions as &$question) {
      $question->variables = $question->variables ? $this->calculateQuestionScore($question) : [];
    }

    return $survey;
  }


  /**
   * Calculates score for a specific Questionnaire.
   */
  private function calculateQuestionScore(Question $question)
  {
    $variables = $question->variables;
    if (empty($variables)) return [];

    $this->activeQuestion = $question;

    foreach ($variables as &$variable) {
      $variable['score'] = $score = $this->calculateVariableScore($variable);
      foreach ($variable['cutoffs'] as &$cutoff) {
        $cutoff['scored'] = $this->hasScored($score, $variable, $cutoff);
      }
    }

    return $variables;
  }

  /**
   * Calculates score for a specific variable.
   */
  private function calculateVariableScore(array $variable): int
  {
    $score = 0;
    [$min, $max] = $this->activeQuestion->getBoundaries();
    foreach ($variable['items'] as $itemId) {
      $score += $this->getItemScore($min, $max, $itemId);
    }
    return $score;
  }


  /**
   * Get the answer for a specific item within a question.
   *
   * @param array $question The question data.
   * @param int $id The ID of the item.
   * @return int The item's answer score.
   * @throws Exception If the item's answer is not found.
   */
  private function getItemScore(int $min, int $max, string $id)
  {
    $question = $this->activeQuestion;

    $index = array_search($id, array_column($question->items, 'id'));
    $item = $question->items[$index];
    $answer = $item['answer'] ?? -1;

    // if answer is -1 it means the answer was skipped
    if ($answer === -1) return 0;

    // throws an error if answer is outside min and max boundaries.
    if ($answer < $min || $answer > $max)
      throw new Exception("La risposta dell'item \"{$item['id']}.{$item['text']}\" del questionario {$question->question} contiene un punteggio non valido: $answer");

    // if item has reversed score reverse the score
    if (isset($item['reversed']) && $item['reversed'])
      $answer = $this->reverseScore($min, $max, $answer);

    // if question type is edi (0,0,0,1,2,3) it needs to calculate the score for the answer
    if ($question->type === 'EDI')
      $answer = $answer - 2 < 0 ? 0 : $answer - 2;
    // if question type is mul the answer score taken by the item multipleAnswers->singleAnswer->points  attribute
    elseif ($question->type === 'MUL')
      $answer = $item['multipleAnswers'][$answer]['points'];

    return $answer;
  }


  /**
   * Reverse the score for reversed score items.
   */
  private function reverseScore(int $min, int $max, int $answer): int
  {
    return $min + $max - $answer;
  }


  /**
   * Checks if the score is outside the cutoff's limits.
   */
  private function hasScored(int $score, array $variable, array $cutoff): bool
  {

    $from = $cutoff['from'];
    $to = $cutoff['to'];


    if (isset($this->patient)) {

      $patient = $this->patient;
      //Some cutoffs are gender based, for example male has a cutoff of 6 and female of 9
      if (isset($variable['genderBased']) && $variable['genderBased']) {
        // if the cutoff is gender based and the patient doesn't have a sex assigned throws an error.
        if (!isset($patient->sex) || !in_array($patient->sex, ['M', 'F', 'O']))
          throw new Exception("Uno dei questionari ha cutoffs basati sul sesso ma {$patient->fname} {$patient->lname} non ha nessun sesso assegnato.");

        // if cutoff is gender based assign to form and to the values for female.
        if ($patient->sex === 'F') {
          $from = $cutoff['femFrom'] ?? null;
          $to = $cutoff['femTo'] ?? null;
        }
      }
    }

    $type = $cutoff['type'];

    // returns if the score is outside the cutoff's limit
    switch ($type) {
      case 'greater-than':
        return $score > $from;
      case 'lesser-than':
        return $score < $from;
      default:
        return $score >= $from && $score <= $to;
    }
  }
}
