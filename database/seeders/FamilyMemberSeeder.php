<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FamilyMember;


class FamilyMemberSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Define family positions data
    $familyMembers = [
      ['family_member_name' => 'Father'],
      ['family_member_name' => 'Mother'],
      ['family_member_name' => 'Son'],
      ['family_member_name' => 'Daughter'],
      ['family_member_name' => 'Others'],
      // Add more positions as needed
    ];

    foreach ($familyMembers as $familyMember) {
      FamilyMember::create($familyMember);
    }
  }
}
