<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescribeMedicines extends Model
{
    use HasFactory;

    protected $table = 'prescribe_medicines';
    protected $primaryKey = 'id';

    protected $fillable = [
        'patient_id',
        'medicine_id',
        'medication_type',
        'quantity',
        'frequency',
        'duration',
        'dosage'
    ];
}