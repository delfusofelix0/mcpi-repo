<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the existing column
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Add the column with updated enum values
        Schema::table('registrations', function (Blueprint $table) {
            $table->enum('status', [
                'Pending',
                'Hired',
                'For Demo',
                'For Interview',
                'Reserved',
                'Viewed',
                'Rejected',
                'Declined',
                'Did Not Respond'
            ])->default('Pending');
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

        // Add back the previous column without the new options
        Schema::table('registrations', function (Blueprint $table) {
            $table->enum('status', [
                'Pending',
                'Hired',
                'For Demo',
                'For Interview',
                'Reserved',
                'Viewed',
                'Rejected'
            ])->default('Pending');
        });
    }
};
