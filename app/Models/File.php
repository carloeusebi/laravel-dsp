<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    protected $fillable = ['path', 'name', 'type', 'patient_id'];


    public function deleteFromStorage(): File
    {
        Storage::delete($this->path);
        return $this;
    }
}
