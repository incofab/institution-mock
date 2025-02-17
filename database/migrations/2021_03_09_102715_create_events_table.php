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
    Schema::create('events', function (Blueprint $table) {
      $table->id('id');

      $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
      $table->string('title');
      $table->text('description')->nullable(true);
      $table->unsignedInteger('duration');
      $table->string('status')->default('active');
      //   $table->boolean('for_external')->default(false);
      $table
        ->foreignId('external_content_id')
        ->nullable()
        ->constrained()
        ->cascadeOnDelete();
      $table->json('external_event_courses')->nullable();

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
    Schema::dropIfExists('events');
  }
};
