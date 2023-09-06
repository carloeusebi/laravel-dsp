<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = ['fname', 'lname',  'birthday', 'birthplace', 'address', 'codice_fiscale', 'begin', 'email', 'phone', 'weight', 'height', 'job', 'qualification', 'sex', 'cohabitants', 'drugs'];

    static function labels(): array
    {
        return [
            'id' => 'id',
            'fname' => 'Nome',
            'lname' => 'Cognome',
            'sex' => 'Sesso',
            'age' => 'Età',
            'birthday' => 'Data di nascita',
            'birthplace' => 'Luogo di nascita',
            'address' => 'Indirizzo',
            'codice_fiscale' => 'Codice Fiscale',
            'begin' => 'Data di inizio Terapia',
            'email' => 'Email',
            'phone' => 'Numero di Telefono',
            'weight' => 'Peso',
            'height' => 'Altezza',
            'qualification' => 'Titolo di Studio',
            'job' => 'Occupazione',
            'cohabitants' => 'Conviventi',
            'drugs' => 'Farmaci'
        ];
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }
}
