<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferredTo extends Model
{
    use HasFactory;

    protected $table = 'referred_to';
}
