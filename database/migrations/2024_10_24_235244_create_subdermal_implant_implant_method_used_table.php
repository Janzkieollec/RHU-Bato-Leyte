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
        Schema::create('subdermal_implant_method_used', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subdermal_implant_id');
            $table->date('date_of_birth');
            $table->integer('no_of_children')->nullable();
            $table->date('date_insertion');
            $table->string('name_of_provider')->nullable();
            $table->string('type_of_provider');
            $table->enum('fp_unmet_method_used', ['limiting', 'spacing']); 
            $table->string('previous_fp_method')->nullable(); 
            $table->integer('quantity');
            $table->timestamps();

            $table->foreign('subdermal_implant_id')->references('id')->on('subdermal_implant')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subdermal_implant_method_used');
    }
};