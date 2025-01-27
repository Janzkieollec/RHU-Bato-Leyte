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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id('address_id');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('family_planning_id');
            $table->unsignedBigInteger('subdermal_implant_id');
            $table->unsignedBigInteger('barangay_id');
            $table->unsignedBigInteger('municipality_id');
            $table->unsignedBigInteger('province_id');
            $table->unsignedBigInteger('region_id');
            $table->unsignedBigInteger('diagnosis_id');
            $table->unsignedBigInteger('diagnosis_type');

      
            // Indexes
            $table->index('patient_id');
            $table->index('family_planning_id');
            $table->index('barangay_id');
            $table->index('municipality_id');
            $table->index('province_id');
            $table->index('region_id');
            $table->index('diagnosis_id');
            $table->index('diagnosis_type');
            
            $table->timestamps();

            $table->foreign('diagnosis_id')->references('diagnosis_id')->on('diagnosis')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};