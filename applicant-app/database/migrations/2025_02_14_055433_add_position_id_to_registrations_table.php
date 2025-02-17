<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPositionIdToRegistrationsTable extends Migration
{
    public function up()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->unsignedBigInteger('work_position_id')->nullable()->after('id');
            $table->foreign('work_position_id')
                  ->references('id')
                  ->on('work_positions')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropForeign(['work_position_id']);
            $table->dropColumn('work_position_id');
        });
    }
}
