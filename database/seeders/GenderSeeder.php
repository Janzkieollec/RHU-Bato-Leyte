<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gender;


class GenderSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $genders = [
      ['gender_name' => 'Male'],
      ['gender_name' => 'Female'],
      ['gender_name' => 'Others'],
      // Add more provinces as needed
    ];

    foreach ($genders as $gender) {
      Gender::create($gender);
    }
  }
}
