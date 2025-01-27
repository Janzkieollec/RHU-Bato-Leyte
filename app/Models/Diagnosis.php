<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{
    use HasFactory;

  protected $table = 'diagnosis';

  protected $fillable = ['diagnosis_name', 'diagnosis_code', 'diagnosis_type'];
  protected $primaryKey = 'diagnosis_id';
  public $timestamps = true; // Enable timestamps


}