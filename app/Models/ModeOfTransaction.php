<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModeOfTransaction extends Model
{
    use HasFactory;

    protected $table = 'mode_of_transaction';

    protected $primaryKey = 'mode_of_transaction_id';
}
