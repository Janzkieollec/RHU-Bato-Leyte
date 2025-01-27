<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Implant extends Model
{
    use HasFactory;

    protected $table ='subdermal_implant';

    protected $fillable = [
        'id',
        'patient_id',
        'age'
    ];

    public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

}