<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients';
    protected $primaryKey = 'patient_id';

    protected $fillable = [
        'patient_id',
        'family_number', 
        'first_name', 
        'last_name', 
        'middle_name', 
        'suffix_name', 
        'birth_date', 
        'age', 
        'gender_id', 
        'contact',
        'created_at'
    ];

    public function address()
    {
        return $this->hasOne(Address::class, 'patient_id', 'patient_id');
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'patient_id', 'patient_id');
    }

    public function dentals()
    {
        return $this->hasMany(Dental::class, 'patient_id', 'patient_id');
    }
    
    
    public function deceased()
    {
        return $this->hasOne(DeceasedPatient::class, 'patient_id', 'patient_id');
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

    public function bloodType()
    {
        return $this->belongsTo(BloodType::class, 'blood_type_id');
    }

    public function treatmentRecords()
    {
        return $this->hasMany(TreatmentRecord::class, 'patient_id', 'patient_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'patient_id');
    }

    public function queue()
    {
        return $this->belongsTo(PatientQueueing::class, 'id', 'patient_id');
    }

}