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
                'Did Not Respond',
                'Recommended'  // Added 'Recommended' to the enum
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
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
            ])->change();
        });
    }
};
