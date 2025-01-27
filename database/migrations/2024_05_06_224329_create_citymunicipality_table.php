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
    Schema::create('refcitymun', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('psgcCode', 255)->nullable();
      $table->text('citymunDesc')->nullable();
      $table->string('regDesc', 255)->nullable();
      $table->string('provCode', 255)->nullable();
      $table->string('citymunCode', 255)->nullable();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('refcitymun');
  }
};
