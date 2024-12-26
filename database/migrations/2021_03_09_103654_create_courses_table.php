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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('institution_id')
                ->nullable()
                ->references('id')
                ->on('institutions');
            $table
                ->foreignId('exam_content_id')
                ->nullable()
                ->references('id')
                ->on('exam_contents');
            $table->string('course_code');
            $table->string('category')->nullable(true);
            $table->string('course_title')->nullable(true);
            $table->text('description')->nullable(true);
            $table->unsignedInteger('order')->default(1000);
            $table->boolean('is_file_content_uploaded')->default(false);
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
        Schema::dropIfExists('courses');
    }
};
