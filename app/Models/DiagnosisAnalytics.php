<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosisAnalytics extends Model
{
    use HasFactory;

    protected $table = 'diagnosis_analytics';
    protected $primaryKey = 'id';

    protected $fillable = [
        'patient_id',
        'diagnosis_id',
        'barangay_name',
        'age',
    ];
      

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
    
    public function diagnosis()
    {
        return $this->belongsTo(Diagnosis::class, 'diagnosis_id');
    }
    
}