<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationDiagnosis extends Model
{
    use HasFactory;

    protected $table = 'consultations_diagnosis';
     
    protected $fillable = [
        'id',
        'patient_id',
        'diagnosis_id',
        'description',
    ];

    // In ConsultationDiagnosis.php
    public function diagnosis()
    {
        return $this->belongsTo(Diagnosis::class, 'diagnosis_id');
    }
    
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function prescribedMedicines()
{
    return $this->hasMany(PrescribedMedicine::class, 'id');
}


}