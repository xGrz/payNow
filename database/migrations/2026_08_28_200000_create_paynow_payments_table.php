<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paynow_payments', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->unsignedBigInteger('amount');
            $table->string('currency_code', 3);
            $table->string('description')->nullable();
            $table->string('email')->nullable();
            $table->text('continue_url')->nullable();
            $table->string('idempotencyKey')->nullable();

            $table->text('link')->nullable();
            $table->string('paynowable_type')->nullable();
            $table->string('paynowable_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paynow_payments');
    }
};
