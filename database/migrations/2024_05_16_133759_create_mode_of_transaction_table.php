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
        Schema::create('mode_of_transaction', function (Blueprint $table) {
          $table->id('mode_of_transaction_id'); // Auto-incrementing primary key
          $table->string('mode_of_transaction_name', 100);
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mode_of_transaction');
    }
};
