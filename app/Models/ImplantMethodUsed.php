<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImplantMethodUsed extends Model
{
    use HasFactory;

    protected $table = 'subdermal_implant_method_used';

    protected $fillable = [
        'patient_id',
        'no_of_children',
        'name_of_provider',
        'type_of_provider',
        'fp_unmet_method_used',
        'previous_fp_method',
        'quantity'
    ];
}