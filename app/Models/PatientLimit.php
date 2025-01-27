<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientLimit extends Model
{
    use HasFactory;

    protected $table = 'patient_limits';
    
    protected $fillable = ['user_id', 'max_patients', 'current_patients'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
    
}