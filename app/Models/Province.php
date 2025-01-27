<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $table = 'refprovince';

    protected $primaryKey = 'id';

    public function address()
    {
        return $this->hasMany(Address::class, 'province_id', 'id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'regCode', 'regCode');
    }

    public function citymunicipality()
    {
        return $this->hasMany(CityMunicipality::class, 'provCode', 'provCode');
    }
}