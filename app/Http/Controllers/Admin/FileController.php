<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf',
            'patient_id' => 'required|exists:patients,id' //Checks the patient_id exists in patients table
        ]);

        $file = $request->file('file');
        $file_name = $file->getClientOriginalName();
        $file_type = $file->getClientOriginalExtension();
        $file_path = $file->store('uploads');

        $id = File::insertGetId([
            'path' => $file_path,
            'name' => $file_name,
            'type' => $file_type,
            'patient_id' => $request->patient_id,
        ]);

        $uploaded_file = File::findOrFail($id);

        return $uploaded_file;
    }

    public function download(string $id)
    {
        $file = File::findOrFail($id);

        $file_path = $file->path;
        $file_name = $file->name;

        if (!$file_path) {
            abort(404, 'Invalid file ID');
        }

        return Storage::download($file_path, 'name');
    }

    public function destroy(string $id)
    {
        $file = File::findOrFail($id);

        // delete the file from the storage
        $file->deleteFromStorage();

        // delete the database record
        $file->delete();
    }
}
