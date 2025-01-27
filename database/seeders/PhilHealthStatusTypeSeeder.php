<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PhilHealthStatusType;


class PhilHealthStatusTypeSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $statuses = [
      ['phil_health_status_type_name' => 'Member'],
      ['phil_health_status_type_name' => 'Dependent'],
      // Add more data as needed
    ];

    foreach ($statuses as $status) {
      PhilHealthStatusType::create($status);
    }
  }
}
