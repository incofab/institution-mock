<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
  public function up()
  {
    Schema::table('fundings', function (Blueprint $table) {
      $table
        ->unsignedInteger('bonus_licenses')
        ->default(0)
        ->after('num_of_licenses');
      $table->text('comment')->nullable()->after('reference');
    });
  }

  public function down()
  {
    Schema::table('fundings', function (Blueprint $table) {
      $table->dropColumn(['bonus_licenses', 'comment']);
    });
  }
};
