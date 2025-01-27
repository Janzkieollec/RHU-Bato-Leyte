<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dental extends Model
{
    use HasFactory;

    protected $table = 'dentals';
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
        'created_at',
        'emergency_purposes'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}