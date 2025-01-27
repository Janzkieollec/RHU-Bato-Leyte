<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CivilStatus;


class CivilStatusSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $civil_statuses = [
      ['civil_status_name' => 'Single'],
      ['civil_status_name' => 'Widowed'],
      ['civil_status_name' => 'Married'],
      ['civil_status_name' => 'Separated'],
      ['civil_status_name' => 'Annulled'],
      ['civil_status_name' => 'Co-Habitation'],
    ];

    foreach ($civil_statuses as $civil_status) {
      CivilStatus::create($civil_status);
    }
  }
}
