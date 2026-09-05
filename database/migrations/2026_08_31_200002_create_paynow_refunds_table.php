<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('paynow_refunds', function(Blueprint $table) {
            $table->id();
            $table->foreignId('paynow_payment_id')->constrained('paynow_payments')->cascadeOnDelete();
            $table->foreignId('paynow_attempt_id')->constrained('paynow_attempts')->cascadeOnDelete();
            $table->bigInteger('amount');
            $table->string('refund_id')->nullable();
            $table->string('status')->nullable();
            $table->string('reason')->nullable();
            $table->string('error')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paynow_refunds');
    }
};
