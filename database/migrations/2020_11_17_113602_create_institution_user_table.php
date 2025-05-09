<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('institution_users', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->references('id')->on('users');
      $table->foreignId('institution_id')->references('id')->on('institutions');
      $table->string('status')->default('active');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('institution_users');
  }
};
