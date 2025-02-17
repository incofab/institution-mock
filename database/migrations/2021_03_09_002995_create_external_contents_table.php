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
    Schema::create('external_contents', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->unsignedBigInteger('content_id');
      $table->json('exam_content');
      $table->string('source');

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
    Schema::dropIfExists('external_contents');
  }
};
