<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paynow_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paynow_payment_id')->nullable()->constrained('paynow_payments')->cascadeOnDelete();
            $table->string('payment_id', 50)->nullable();
            $table->string('status', 30)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paynow_attempts');
    }
};
