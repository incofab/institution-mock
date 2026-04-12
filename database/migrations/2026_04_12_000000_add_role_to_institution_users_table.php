<?php

use App\Enums\InstitutionUserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::table('institution_users', function (Blueprint $table) {
      $table
        ->string('role')
        ->default(InstitutionUserRole::Admin->value)
        ->after('status');
    });
  }

  public function down()
  {
    Schema::table('institution_users', function (Blueprint $table) {
      $table->dropColumn('role');
    });
  }
};
