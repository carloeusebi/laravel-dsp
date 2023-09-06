<?php

namespace App\Helpers;

use App\Models\Patient;
use App\Models\Question;
use App\Models\Survey;
use Exception;

class Results
{

  private Patient $patient;
  private Question $activeQuestion;

  public function __construct(private Survey $survey)
  {
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
      $question->variables = $this->calculateQuestionScore($question);
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
      $score = $this->calculateVariableScore($variable);
      $variable['score'] = $score;
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
    foreach ($variable['items'] as $itemId) {
      $score += $this->getItemScore($itemId);
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
  private function getItemScore(string $id)
  {
    $question = $this->activeQuestion;
    [$min, $max] = $this->activeQuestion->getBoundaries();

    $index = array_search($id, array_column($question->items, 'id'));
    $item = $question->items[$index];
    $answer = $item['answer'] ?? -1;

    // if answer is -1 it means the answer was skipped
    if ($answer === -1) return 0;

    if ($answer < $min || $answer > $max)
      throw new Exception("La risposta dell'item \"{$item['id']}.{$item['text']}\" del questionario {$question->question} contiene un punteggio non valido: $answer");

    if (isset($item['reversed']) && $item['reversed'])
      $answer = $this->reverseScore($min, $max, $answer);

    if ($question->type === 'EDI')
      $answer = $answer - 2 < 0 ? 0 : $answer - 2;
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


  private function hasScored(int $score, array $variable, array $cutoff): bool
  {
    $patient = $this->patient;

    $from = $cutoff['from'];
    $to = $cutoff['to'];

    if (isset($variable['genderBased']) && $variable['genderBased']) {
      if (!isset($patient->sex) || !in_array($patient->sex, ['M', 'F', 'O']))
        throw new Exception("Uno dei questionari ha cutoffs basati sul sesso ma {$patient->fname} {$patient->lname} non ha nessun sesso assegnato.");

      if ($patient->sex === 'F') {
        $from = $cutoff['femFrom'] ?? null;
        $to = $cutoff['femTo'] ?? null;
      }
    }

    $type = $cutoff['type'];

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
