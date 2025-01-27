<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationQueue extends Model
{
    use HasFactory;

    protected $table = 'consultation_queueing';
    
    protected $fillable = ['id', 'patient_id', 'created_at', 'updated_at'];
    
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}