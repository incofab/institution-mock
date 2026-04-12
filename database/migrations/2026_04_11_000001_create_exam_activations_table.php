<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::create('exam_activations', function (Blueprint $table) {
      $table->id();
      $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
      $table->foreignId('event_id')->constrained()->cascadeOnDelete();
      $table
        ->foreignId('activated_by_user_id')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();
      $table->unsignedInteger('num_of_exams')->default(1);
      $table->unsignedInteger('licenses')->default(1);
      $table->unsignedInteger('license_balance_before');
      $table->unsignedInteger('license_balance_after');
      $table->timestamp('activated_at')->useCurrent();
      $table->timestamps();
    });

    Schema::table('exams', function (Blueprint $table) {
      $table
        ->foreignId('exam_activation_id')
        ->nullable()
        ->after('event_id')
        ->constrained('exam_activations')
        ->nullOnDelete();
    });
  }

  public function down()
  {
    Schema::table('exams', function (Blueprint $table) {
      $table->dropConstrainedForeignId('exam_activation_id');
    });

    Schema::dropIfExists('exam_activations');
  }
};
