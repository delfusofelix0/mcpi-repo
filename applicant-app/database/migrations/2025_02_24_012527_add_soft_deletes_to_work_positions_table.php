<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToWorkPositionsTable extends Migration
{
    public function up(): void
    {
        Schema::table('work_positions', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('work_positions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
