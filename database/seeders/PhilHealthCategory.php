<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PhilHealthCategories;


class PhilHealthCategory extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $categories = [
      ['phil_health_category_name' => 'FE - Private'],
      ['phil_health_category_name' => 'FE - Government'],
      ['phil_health_category_name' => 'IE'],
      ['phil_health_category_name' => 'Others'],
      // Add more data as needed
    ];

    foreach ($categories as $category) {
      PhilHealthCategories::create($category);
    }
  }
}
