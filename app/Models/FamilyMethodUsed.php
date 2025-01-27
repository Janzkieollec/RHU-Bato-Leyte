<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyMethodUsed extends Model
{
    use HasFactory;

    protected $table = 'family_method_used';

    protected $fillable = [
        'id',
        'patient_id',
        'quantity',
        'fp_method_used',
        'nhts_non-nhts'
    ];

    public function planning()
    {
        return $this->belongsTo(FamilyPlanning::class, 'family_planning_id', 'family_planning_id');
    }
}