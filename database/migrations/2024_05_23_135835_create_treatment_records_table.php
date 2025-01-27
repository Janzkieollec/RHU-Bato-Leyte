<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('treatment_records', function (Blueprint $table) {
          $table->id('patient_record_id');
          $table->unsignedBigInteger('patient_id');
          $table->unsignedBigInteger('mode_of_transaction_id');
          $table->date('date_of_consultation');
          $table->string('blood_pressure', 50)->nullable();
          $table->string('body_temperature', 50)->nullable();
          $table->string('height', 50)->nullable();
          $table->string('weight', 50)->nullable();
          $table->unsignedBigInteger('attending_provider_id');
          $table->unsignedBigInteger('referred_from_id')->nullable();
          $table->unsignedBigInteger('referred_to_id')->nullable();
          $table->string('reasons_of_referral', 255)->nullable();
          $table->unsignedBigInteger('referred_by_id')->nullable();
          $table->unsignedBigInteger('nature_of_visit_id');
          $table->unsignedBigInteger('type_of_consultation_id');
          $table->string('chief_complaints', 255)->nullable();
          $table->unsignedBigInteger('diagnosis_id');
          $table->string('medication_treatment', 255)->nullable();
          $table->unsignedBigInteger('healthcare_provider_id')->nullable();
          $table->string('laboratory_findings', 255)->nullable();
          $table->timestamps();

          // Adding foreign key constraints
          $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('cascade')->onUpdate('cascade');
          $table->foreign('mode_of_transaction_id')->references('mode_of_transaction_id')->on('mode_of_transaction')->onDelete('cascade')->onUpdate('cascade');
          $table->foreign('attending_provider_id')->references('attending_provider_id')->on('attending_providers')->onDelete('cascade')->onUpdate('cascade');
          $table->foreign('referred_from_id')->references('referred_from_id')->on('referred_from')->onDelete('cascade')->onUpdate('cascade');
          $table->foreign('referred_to_id')->references('referred_to_id')->on('referred_to')->onDelete('cascade')->onUpdate('cascade');
          $table->foreign('referred_by_id')->references('referred_by_id')->on('referred_by')->onDelete('cascade')->onUpdate('cascade');
          $table->foreign('nature_of_visit_id')->references('nature_of_visit_id')->on('nature_of_visit')->onDelete('cascade')->onUpdate('cascade');
          $table->foreign('type_of_consultation_id')->references('type_of_consultation_id')->on('type_of_consultation')->onDelete('cascade')->onUpdate('cascade');
          $table->foreign('healthcare_provider_id')->references('healthcare_provider_id')->on('healthcare_provider')->onDelete('cascade')->onUpdate('cascade');
          $table->foreign('diagnosis_id')->references('diagnosis_id')->on('diagnosis')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_records');
    }
};
