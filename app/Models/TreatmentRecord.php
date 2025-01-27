<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentRecord extends Model
{
    use HasFactory;

    protected $table = 'treatment_records';
    protected $primaryKey = 'patient_record_id';

    protected $fillable = ['patient_id', 'mode_of_transaction_id', 'date_of_consultation', 'blood_pressure', 'body_temperature', 'height', 'weight', 'attending_provider_id', 'referred_from_id', 'referred_to_id', 'reasons_of_referral', 'referred_by_id', 'nature_of_visit_id', 'type_of_consultation_id', 'chief_complaints', 'diagnosis_id', 'medication_treatment', 'healthcare_provider_id', 'laboratory_findings'];

    public function patient(){
      return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function modeOfTransaction()
    {
        return $this->belongsTo(ModeOfTransaction::class, 'mode_of_transaction_id', 'mode_of_transaction_id');
    }
}