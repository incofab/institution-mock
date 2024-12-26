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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('institution_id')
                ->references('id')
                ->on('institutions');
            $table->foreignId('event_id')->references('id')->on('events');
            $table->string('exam_no')->unique();
            $table->foreignId('student_id')->references('id')->on('students');

            // $table->unsignedInteger('duration');
            $table->float('time_remaining');

            $table->dateTime('start_time')->nullable(true);
            $table->dateTime('pause_time')->nullable(true);
            $table->dateTime('end_time')->nullable(true);
            $table->unsignedInteger('score')->nullable(true);
            $table->unsignedInteger('num_of_questions')->nullable(true);
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
        Schema::dropIfExists('exams');
    }
};
