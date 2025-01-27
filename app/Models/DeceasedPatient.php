<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeceasedPatient extends Model
{
    use HasFactory;

    protected $table = 'deceased_patients';

    // Define the primary key of the table
    protected $primaryKey = 'deceased_patients_id';

    protected $fillable = ['patient_id', 'date_of_death', 'cause_of_death'];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}