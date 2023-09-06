<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionRequest;
use App\Models\Question;
use Illuminate\Http\Request;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class QuestionsController extends Controller
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

        $question = Question::find($id);

        // if questionnaire is currently used in surveys, hide it and make a copy
        if ($question->isInSurveys()) {
            $question->not_current = true;
            $question->update();
            $question->tags()->detach(); // also detach all tags

            $question = Question::create($data);
        } else {
            $question->update($data);
        }

        // sync the tags
        $tagIds = array_map(fn ($tag) => $tag['id'], $request->tags) ?? [];
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
