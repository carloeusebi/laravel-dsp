<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionRequest;
use App\Models\Question;
use App\Services\QuestionVersionControlService;
use Illuminate\Http\Request;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $labels = Question::labels();
        $questions = Question::with('tags')->whereNull('not_current')->orderBy('question', 'asc')->get();

        return ['labels' => $labels, 'list' => $questions];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuestionRequest $request)
    {
        $data = $request->all();

        $question = Question::create($data);

        return $question;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(QuestionRequest $request, string $id)
    {
        $data = $request->all();
        /**
         * @var Question
         */
        $existingQuestion = Question::findOrFail($id);

        // if questionnaire is currently used in surveys, hide it and make a copy
        if ($existingQuestion->isInSurveys() && $existingQuestion->differencesAreBigEnough($data)) {
            $existingQuestion->not_current = true;
            $existingQuestion->tags()->detach(); // also detach all tags
            $existingQuestion->update();

            $question = Question::create($data);
        } else {
            $existingQuestion->update($data);
            $question = $existingQuestion;
        }

        // sync the tags
        $tagIds = $request->tags ? array_map(fn ($tag) => $tag['id'], $request->tags) : [];
        $question->tags()->sync($tagIds);
        $question->tags = $question->tags()->get();

        return $question;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $question = Question::find($id);

        // if questionnaire is currently used in surveys soft delete it
        if ($question->isInSurveys()) {
            $question->not_current = true;
            $question->update();
            $question->tags()->detach();
        } else {
            $question->delete();
        }
    }
}
