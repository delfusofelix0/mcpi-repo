<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkPositionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('work_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable(); // Add this line for the description
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_positions');
    }
};
