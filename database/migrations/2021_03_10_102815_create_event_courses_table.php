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
        Schema::create('event_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->references('id')->on('events');
            $table
                ->foreignId('course_session_id')
                ->references('id')
                ->on('course_sessions');
            $table->string('status')->default('active');
            $table->unsignedInteger('num_of_questions')->nullable(true);
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
        Schema::dropIfExists('event_courses');
    }
};
