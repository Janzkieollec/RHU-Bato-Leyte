<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $table = 'consultations';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'patient_id',
        'blood_pressure',
        'body_temperature',
        'height',
        'weight',
        'chief_complaints',
        'number_of_days',
        'emergency_purposes',
        'created_at'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}