<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BloodType;

class BloodTypeSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $bloodTypes = [
      ['blood_type_name' => 'A+'],
      ['blood_type_name' => 'A-'],
      ['blood_type_name' => 'B+'],
      ['blood_type_name' => 'B-'],
      ['blood_type_name' => 'AB+'],
      ['blood_type_name' => 'AB-'],
      ['blood_type_name' => 'O+'],
      ['blood_type_name' => 'O-'],
      ['blood_type_name' => 'Unknown'],
    ];

    foreach ($bloodTypes as $bloodType) {
      BloodType::create($bloodType);
    }
  }
}
