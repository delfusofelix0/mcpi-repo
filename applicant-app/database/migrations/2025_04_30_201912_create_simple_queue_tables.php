<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Service windows (just 2 cashier windows)
        Schema::create('windows', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Cashier 1", "Cashier 2"
            $table->string('department')->nullable(); // e.g., "cashier", "accounting", "registrar"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tickets table
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number'); // e.g., 001, 002, etc.
            $table->string('department'); // e.g., "cashier", "accounting", "registrar"
            $table->dateTime('issue_time');
            $table->dateTime('call_time')->nullable();
            $table->dateTime('completion_time')->nullable();
            $table->foreignId('window_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['waiting', 'serving', 'completed', 'skipped'])->default('waiting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('windows');
    }
};
