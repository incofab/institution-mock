<?php

use App\Models\Institution;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::table('institutions', function (Blueprint $table) {
      $table->unsignedInteger('licenses')->default(0)->after('status');
      $table
        ->decimal('license_cost', 12, 2)
        ->default(Institution::DEFAULT_LICENSE_COST)
        ->after('licenses');
    });

    Schema::create('fundings', function (Blueprint $table) {
      $table->id();
      $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
      $table->foreignId('user_id')->constrained()->cascadeOnDelete();
      $table->decimal('amount', 12, 2);
      $table->decimal('license_cost', 12, 2);
      $table->unsignedInteger('num_of_licenses');
      $table->decimal('balance_amount', 12, 2)->default(0);
      $table->unsignedInteger('license_balance_before');
      $table->unsignedInteger('license_balance_after');
      $table->string('source')->default('manual');
      $table->string('reference')->nullable();
      $table->nullableMorphs('fundable');
      $table->timestamps();
    });

    Schema::create('gateway_payments', function (Blueprint $table) {
      $table->id();
      $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
      $table->foreignId('user_id')->constrained()->cascadeOnDelete();
      $table->string('gateway');
      $table->string('reference')->unique();
      $table->decimal('amount', 12, 2);
      $table->string('status')->default('pending');
      $table->json('gateway_payload')->nullable();
      $table->timestamp('initialized_at')->nullable();
      $table->timestamp('verified_at')->nullable();
      $table->timestamp('failed_at')->nullable();
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('gateway_payments');

    Schema::dropIfExists('fundings');

    Schema::table('institutions', function (Blueprint $table) {
      $table->dropColumn(['licenses', 'license_cost']);
    });
  }
};
