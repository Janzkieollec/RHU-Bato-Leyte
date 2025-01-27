<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyPlanning extends Model
{
    use HasFactory;

    protected $table = 'family_planning';
    
    protected $fillable = [
        'family_planning_id',
        'patient_id',
        'age',
        'created_at'
    ];
    
    protected $primaryKey = 'family_planning_id';


    public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'patient_id', 'patient_id');
    }
    
    public function method()
    {
        return $this->hasOne(FamilyMethodUsed::class, 'family_planning_id', 'family_planning_id');
    }
}