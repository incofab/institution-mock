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
    Schema::create('course_sessions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('course_id')->constrained()->cascadeOnDelete();
      $table->string('session');
      $table->string('category')->nullable(true);
      $table->text('general_instructions')->nullable(true);
      $table->integer('file_version', false, true)->default(0);
      $table->string('file_path')->nullable();
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
    Schema::dropIfExists('course_sessions');
  }
};
