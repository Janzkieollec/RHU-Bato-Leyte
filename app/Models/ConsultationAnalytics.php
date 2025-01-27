<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationAnalytics extends Model
{
    use HasFactory;

    protected $table = 'consultation_analytics';

    protected $fillable = [
        'id',
        'patient_id',
        'consultation_id',
        'barangay_name',
        'age'
    ];
}