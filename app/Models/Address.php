<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $table = 'addresses';

    protected $fillable = ['patient_id', 'family_planning_id', 'subdermal_implant_id', 'barangay_id', 'municipality_id', 'province_id', 'region_id', 'diagnosis_id'];

    protected $primaryKey = 'address_id';

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function planning()
    {
        return $this->belongsTo(FamilyPlanning::class, 'patient_id', 'patient_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'id', 'brgyCode');
    }
    
    public function municipality()
    {
        return $this->belongsTo(CityMunicipality::class, 'municipality_id', 'citymunCode');
    }
    
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'provCode');
    }

    public function diagnosis()
    {
        return $this->belongsTo(Diagnosis::class, 'diagnosis_id', 'diagnosis_id'); // Ensure the foreign key and local key are correct
    }
        
}