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
        Schema::create('family_method_used', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('family_planning_id');
            $table->integer('quantity');
            $table->string('fp_method_used');
            $table->boolean('nhts_non-nhts');
            $table->timestamps();

            $table->foreign('family_planning_id')->references('family_planning_id')->on('family_planning')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_method_used');
    }
};