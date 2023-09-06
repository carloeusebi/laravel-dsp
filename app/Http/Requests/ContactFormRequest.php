<?php

namespace App\Http\Requests;

use App\Services\VerifaliaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class ContactFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'phone' => 'numero di telefono',
            'mail' => 'indirizzo email',
            'message' => 'messaggio'
        ];
    }


    public function messages(): array
    {
        return [
            'required' => 'Il campo :attribute è obbligatorio',
            'norm-cb.required' => 'Bisogna accettare la normativa sui dati personali',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'phone' => 'required',
            'mail' => 'required|email',
            'message' => 'required',
            'norm-cb' => 'required',
        ];
    }


    public function withValidator(Validator $validator)
    {
        $validator->after(
            function ($validator) {

                // if honeybox is checked it means probably a bot tried to submit the form.
                $honeybox = $this->input('miele-cb');
                if ($honeybox) {
                    $validator->errors()->add('miele-cb', 'Qualcosa è andato storto. Si prega di riprovare.');
                    // logs the bot attempt to the database
                    DB::table('logs')->insert(['message' => 'Un bot ha provato ad inviare il form', 'created_at' => date('Y-m-d H:i:s', time())]);
                }

                // skips Verifalia validation if other checks failed
                if (!$validator->errors()->all()) {
                    $email = $this->input('mail');
                    if (!VerifaliaService::emailIsValid($email)) {
                        $validator->errors()->add('mail', 'L\'email non è valida, inserire un indirizzo email valido');
                    }
                }
            }
        );
    }
}
