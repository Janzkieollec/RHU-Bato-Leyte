<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EducationalAttainment;


class EducationalAttainmentSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Define the educational attainments data
    $educationalAttainments = [
      ['educational_attainment_name' => 'No Formal Education'],
      ['educational_attainment_name' => 'Elementary'],
      ['educational_attainment_name' => 'High School'],
      ['educational_attainment_name' => 'Vocational'],
      ['educational_attainment_name' => 'College'],
      ['educational_attainment_name' => 'Post Graduate'],
      // Add more data as needed
    ];

    foreach ($educationalAttainments as $educationalAttainment) {
      EducationalAttainment::create($educationalAttainment);
    }
  }
}
