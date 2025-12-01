<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiseasePrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'encounter_id', 'prediction', 'probability', 'risk_level', 'recommendations'
    ];

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }
}
