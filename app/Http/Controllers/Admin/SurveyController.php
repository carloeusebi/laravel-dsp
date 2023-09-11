<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Results;
use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Survey::with('patient')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validation
        $request->validate([
            'title' => 'required|string|max:80',
            'patient_id' => 'required|exists:patients,id',
            'questionIds' => 'required',
        ]);

        $data = $request->all();
        $survey = new Survey($data);
        $survey->token = $survey->generateToken();
        $survey->save();

        $survey->questions()->attach($request->questionIds);

        $survey->patient = $survey->patient()->first();

        return $survey;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $survey = Survey::with('questions')->with('patient')->findOrFail($id);

        foreach ($survey->questions as &$question) {
            $question->bindAnswers();
        }

        return $survey;
    }


    /**
     * Updates survey's answers.
     */
    public function update(Request $request, string $id)
    {
        /**
         * @var Survey
         */
        $survey = Survey::with('questions')->findOrFail($id);
        $data = $request->all();

        foreach ($data['questions'] as $question) {
            //filters the items removing all items without an answer
            $itemsWithAnswer = array_values(array_filter($question['items'], fn ($item) => isset($item['answer'])));

            // crates an array of answers in the form [itemId, answer]
            $answers = array_map(function ($item) {
                $answer = ['id' => $item['id'], 'answer' => $item['answer']];
                if (isset($item['comment'])) $answer['comment'] = $item['comment'];
                return $answer;
            }, $itemsWithAnswer);

            //updates the pivot table
            $isQuestionCompleted = count($answers) === count($question['items']);
            $survey->questions()->updateExistingPivot($question['id'], ['answers' => $answers, 'completed' => $isQuestionCompleted]);
        }

        $survey->completed = $survey->isCompleted();
        $survey->save();

        return response(status: 204);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Survey::destroy($id);
    }


    public function calculateResults(string $id)
    {
        $survey = Survey::with('patient')->with('questions')->findOrFail($id);

        if (!$survey->completed) {
            return response(['message' => "Questo test non è stato ancora completato."], 204);
        }

        foreach ($survey->questions as &$question) {
            $question->bindAnswers();
        }

        try {
            $calculator = new Results($survey);
            $results = $calculator->calculate();
        } catch (\Exception $e) {
            return response(['message' => $e->getLine() . '.' . $e->getMessage()], 422);
        }

        return $results;
    }
}
