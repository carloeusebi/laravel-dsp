<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Models\Patient;
use App\Models\Question;
use App\Models\Survey;
use Illuminate\Http\Request;

class TestsController extends Controller
{
    public function show(string $token)
    {
        $survey = Survey::with('patient')
            ->with('questions')
            ->where('token', '=', $token,)
            ->where('completed', '=', '0')
            ->first();

        if (!$survey) return response()->json('No Test found', 404);

        foreach ($survey->questions as &$question) {
            // binds the answer from the pivot column
            $question->bindAnswers();
        }

        return $survey;
    }


    public function update(Request $request, string $id)
    {
        if (!$request->questionId) {
            abort(400, 'No Question ID');
        }
        $answers = $request->answers;

        $survey = Survey::find($id);
        $question = Question::find($request->questionId);

        $survey->updated_at = now();

        $questionCompleted = count($answers) === count($question->items);

        $survey->questions()->updateExistingPivot(
            $question,
            [
                'answers' => $answers ?? [],
                'completed' => $questionCompleted,
            ]
        );

        $survey->completed = $survey->isCompleted();
        $survey->save();

        return response(status: 204);
    }


    public function updatePatientInfo(PatientRequest $request, string $id)
    {
        $data = $request->all();

        // updates empty fields, this is to prevent patient from deleting its own informations
        $fieldsToUpdate = array_filter($data);

        Patient::find($id)->update($fieldsToUpdate);

        return response(status: 204);
    }
}
