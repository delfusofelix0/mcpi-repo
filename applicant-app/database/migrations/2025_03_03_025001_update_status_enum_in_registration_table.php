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
        // First, update any existing 'Option' records to 'Pending'
        DB::table('registrations')->where('status', 'Option')->update(['status' => 'Pending']);

        // Drop the existing column
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Add the column with new enum values
        Schema::table('registrations', function (Blueprint $table) {
            $table->enum('status', ['Pending', 'Hired', 'For Demo', 'For Interview', 'Reserved', 'Viewed', 'Rejected'])->default('Pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the modified column
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Add back the original column
        Schema::table('registrations', function (Blueprint $table) {
            $table->enum('status', ['Pending', 'Hired', 'Option', 'Viewed', 'Rejected'])->default('Pending');
        });
    }
};
