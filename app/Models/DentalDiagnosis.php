<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DentalDiagnosis extends Model
{
    use HasFactory;

    protected $table = 'dentals_diagnosis';
     
    protected $fillable = [
        'patient_id',
        'diagnosis_id',
        'description',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}