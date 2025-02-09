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
    Schema::create('exam_courses', function (Blueprint $table) {
      $table->id();
      $table->foreignId('exam_id')->references('id')->on('exams');
      $table
        ->unsignedBigInteger('course_session_id')
        ->comment(
          'No foreign relationship because course_session_id could be coming from external source',
        );
      $table->unsignedInteger('score')->nullable(true);
      $table->unsignedInteger('num_of_questions')->nullable(true);
      $table->string('status')->default('active');
      $table->string('course_code');
      $table->string('session')->nullable();
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
    Schema::dropIfExists('exam_courses');
  }
};
