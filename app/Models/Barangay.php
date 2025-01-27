<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    use HasFactory;

    protected $table = 'refbrgy';

    protected $primaryKey = 'id';

    public function address()
    {
        return $this->hasMany(Address::class, 'barangay_id', 'id');
    }

    public function cityMunicipality()
    {
        return $this->belongsTo(CityMunicipality::class, 'citymunCode', 'citymunCode');
    }
}