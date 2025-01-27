<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DentalAnalytics extends Model
{
    use HasFactory;

    protected $table = 'dental_analytics';

    protected $fillable = [
        'patient_id',
        'dental_id',
        'barangay_name',
        'age'
    ];
}