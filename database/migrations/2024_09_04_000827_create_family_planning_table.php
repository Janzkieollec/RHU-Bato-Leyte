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
        Schema::create('family_planning', function (Blueprint $table) {
            $table->id('family_planning_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name');
            $table->string('suffix_name');
            $table->unsignedBigInteger('gender_id');
            $table->date('birth_date');
            $table->timestamps();

            $table->foreign('gender_id')->references('gender_id')->on('genders')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_planning');
    }
};