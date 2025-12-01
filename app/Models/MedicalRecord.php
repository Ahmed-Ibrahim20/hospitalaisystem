<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'encounter_id', 'diagnosis', 'notes'
    ];

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }
}
