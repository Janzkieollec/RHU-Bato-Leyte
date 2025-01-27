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
        Schema::create('deceased_patients', function (Blueprint $table) {
            $table->increments('deceased_patients_id');
            $table->unsignedBigInteger('patient_id'); // Ensure this matches the patients table type
            $table->date('date_of_death');
            $table->string('cause_of_death', 255)->collation('utf8mb4_general_ci');

            // Adding the foreign key constraint
            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deceased_patients');
    }
};
