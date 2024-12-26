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
                ->foreignId('course_session_id')
                ->references('id')
                ->on('course_sessions');
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
        Schema::dropIfExists('exam_courses');
    }
};
