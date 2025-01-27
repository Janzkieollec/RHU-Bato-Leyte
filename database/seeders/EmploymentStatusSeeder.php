<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmploymentStatus;


class EmploymentStatusSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $employmentStatuses = [
      ['employment_status_name' => 'Student'],
      ['employment_status_name' => 'Unknown'],
      ['employment_status_name' => 'Employed'],
      ['employment_status_name' => 'Retired'],
      ['employment_status_name' => 'None/Unemployed'],
      // Add more statuses as needed
    ];

    foreach ($employmentStatuses as $employmentStatus) {
      EmploymentStatus::create($employmentStatus);
    }
  }
}
