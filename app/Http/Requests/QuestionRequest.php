<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class QuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'question' => 'required|string|max:100',
            'description' => 'required|string',
            'type' => 'required|string|min:3|max:3'
        ];
    }


    /**
     * Performs additional validation.
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $variables = $this->input('variables');
            if ($variables) {
                $i = 0;
                foreach ($this->variables as $variable) {
                    if (!isset($variable['name']) || strlen($variable['name']) === 0)
                        $validator->errors()->add('variables', "La variabile numero $i non ha un nome.");
                    $i++;
                }
            }
        });
    }
}
