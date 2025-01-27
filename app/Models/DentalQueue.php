<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DentalQueue extends Model
{
    use HasFactory;

    protected $table = 'dental_queueing';
    
    protected $fillable = ['patient_id', 'created_at', 'updated_at'];
    
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}