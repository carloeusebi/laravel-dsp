<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PatientRequest;
use App\Models\Patient;

class PatientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patients = Patient::with('files')->get();
        $labels = Patient::labels();

        return [
            'labels' => $labels,
            'list' => $patients
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PatientRequest $request)
    {
        $data = $request->all();
        $data['begin'] = $data['begin'] ?? now(); // If therapy begin was not 
        $patient = Patient::create($data);

        return $patient;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return  Patient::with('files')->find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PatientRequest $request, string $id)
    {
        $data = $request->all();

        /**
         * @var Patient
         */
        $patient = Patient::find($id);
        $patient->update($data);

        return $patient;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $patient = Patient::find($id);
        /**
         * @var File[] the to delete patient's files
         */
        $files = $patient->files;

        // delete all the patient's file
        foreach ($files as $file) {
            $file->deleteFromStorage()->delete();
        }

        // finally delete the patient
        $patient->delete();
    }
}
