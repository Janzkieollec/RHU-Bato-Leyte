<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $table = 'refregion';

    protected $primaryKey = 'id';

    public function address()
    {
        return $this->hasMany(Address::class, 'region_id', 'id');
    }

    public function provinces()
    {
        return $this->hasMany(Province::class, 'regCode', 'regCode');
    }

   // Define the relationship with the UserProfile model
   public function profile()
   {
       // Assuming that UserProfile has the 'region_id' field that connects to Region
       return $this->hasOne(UserProfile::class, 'region_id', 'id');
   }
}