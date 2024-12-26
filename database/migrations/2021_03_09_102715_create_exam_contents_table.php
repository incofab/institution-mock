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
        Schema::create('exam_contents', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('institution_id')
                ->nullable()
                ->references('id')
                ->on('institutions');
            $table->string('exam_name')->unique();
            $table->string('fullname')->nullable(true);
            $table->text('description')->nullable(true);
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
        Schema::dropIfExists('exam_contents');
    }
};
