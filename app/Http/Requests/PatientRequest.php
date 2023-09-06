<?php

namespace App\Http\Requests;

use App\Services\CodiceFiscale;
use App\Models\Patient;
use App\Services\VerifaliaService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

class PatientRequest extends FormRequest
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
        return Patient::labels();
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'fname' => 'required|max:80',
            'lname' => 'required|max:80',
            'sex' => 'nullable|max:1',
            'birthday' => 'date|nullable',
            'birthplace' => 'nullable|max:80',
            'address' => 'nullable|max:150',
            'begin' => 'date|nullable',
            'email' => 'nullable|email|max:80',
            'phone' => 'nullable|max:20',
            'weight' => 'nullable|numeric|min:10|max:350',
            'height' => 'nullable|numeric|min:10|max:255',
            'qualification' => 'nullable|max:80',
            'job' => 'nullable|max:50',
        ];
    }

    /**
     * Performs additional validation:
     * Checks if both birthday and therapy begin's date are valid.
     * Checks if codice fiscale is valid.
     * Checks if email is deliverable.
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(
            function ($validator) {
                // checks that the begin date is not in the future
                $begin = $this->input('begin') ?? '';
                if ($begin && $this->isFutureDate($begin)) {
                    $validator->errors()->add('begin', 'La data di inizio terapia non può essere nel futuro.');
                }

                // checks birthday
                $birthday = $this->input('birthday') ?? '';
                if ($birthday && !$validator->errors()->get('birthday')) {
                    $age = Carbon::now()->diffInYears($birthday);
                    if ($age <= 0 || $age > 120)
                        $validator->errors()->add('birthday', "Data di nascita non valida, $age non è un'età valida.");
                }

                // checks codice fiscale
                $codice_fiscale = $this->input('codice_fiscale') ?? '';
                if ($codice_fiscale) {
                    $codice_fiscale_errors = CodiceFiscale::validate($codice_fiscale);
                    if ($codice_fiscale_errors)
                        $validator->errors()->add('codice_fiscale', $codice_fiscale_errors);
                }


                // if email has passed initial validation, performs additional validation, using Verifalia to check the address is deliverable
                $email = $this->input('email');
                if ($email  && !$validator->errors()->all()) {
                    if (!VerifaliaService::emailIsValid($email)) {
                        $validator->errors()->add('email', 'La Email inserita non è un indirizzo valido.');
                    }
                }
            }
        );
    }

    /**
     * Checks if date is in the future.
     */
    public function isFutureDate(string $date): bool
    {
        $current_date = new DateTime();
        $to_validate_date = new DateTime($date);

        return $to_validate_date > $current_date;
    }
}
