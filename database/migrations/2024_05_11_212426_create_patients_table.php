<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('patients', function (Blueprint $table) {
      $table->id('patient_id');
      $table->string('first_name', 150);
      $table->string('last_name', 150);
      $table->string('middle_name', 150)->nullable();
      $table->string('suffix_name', 50)->nullable();
      $table->string('maiden_name', 150)->nullable();
      $table->unsignedBigInteger('gender_id');
      $table->date('birth_date');
      $table->string('mother_name', 150)->nullable();
      $table->string('contact_number', 20);
      $table->string('birth_place', 150);
      $table->unsignedBigInteger('blood_type_id');
      $table->unsignedBigInteger('civil_status_id');
      $table->string('spouse_name', 150)->nullable();
      $table->unsignedBigInteger('educational_attainment_id');
      $table->unsignedBigInteger('employment_status_id');
      $table->unsignedBigInteger('family_member_id');
      $table->boolean('dswd_nhts');
      $table->string('facility_household_no', 100)->nullable();
      $table->boolean('fourps_member');
      $table->string('household_no', 100)->nullable();
      $table->boolean('phil_health_member');
      $table->unsignedBigInteger('phil_health_status_type_id')->nullable();
      $table->string('phil_health_no', 100)->nullable();
      $table->unsignedBigInteger('phil_health_category_id')->nullable();
      $table->boolean('pcb');
      $table->timestamps();
      $table->boolean('is_deceased')->default(false);
      $table->unsignedBigInteger('user_id');


      // Foreign Key Constraints
      $table->foreign('blood_type_id')->references('blood_type_id')->on('blood_types')->onDelete('cascade')->onUpdate('cascade');
      $table->foreign('civil_status_id')->references('civil_status_id')->on('civil_statuses')->onDelete('cascade')->onUpdate('cascade');
      $table->foreign('educational_attainment_id')->references('educational_attainment_id')->on('educational_attainments')->onDelete('cascade')->onUpdate('cascade');
      $table->foreign('employment_status_id')->references('employment_status_id')->on('employment_statuses')->onDelete('cascade')->onUpdate('cascade');
      $table->foreign('family_member_id')->references('family_member_id')->on('family_members')->onDelete('cascade')->onUpdate('cascade');
      $table->foreign('phil_health_status_type_id')->references('phil_health_status_type_id')->on('phil_health_status_types')->onDelete('cascade')->onUpdate('cascade');
      $table->foreign('phil_health_category_id')->references('phil_health_category_id')->on('phil_health_categories')->onDelete('cascade')->onUpdate('cascade');
      $table->foreign('gender_id')->references('gender_id')->on('genders')->onDelete('cascade')->onUpdate('cascade');
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('patients');
  }
};