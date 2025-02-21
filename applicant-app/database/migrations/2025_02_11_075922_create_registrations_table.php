<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('mi')->nullable()->default("N/A");
            $table->string('lastname');
            $table->string('suffix')->nullable()->default("N/A");
            $table->string('email');
            $table->string('phone');
            $table->string('religion')->nullable()->default("Prefers not to say");
            $table->string('sogie');
            $table->date('birthdate');
            $table->text('address');
            $table->string('highest_education');
            $table->string('latest_company')->nullable()->default("N/A");
            $table->string('present_position')->nullable()->default("N/A");;
            $table->string('status_employment')->nullable()->default("N/A");;
            $table->date('last_employment_date')->nullable()->default("N/A");;
            $table->string('eligibility')->nullable()->default("N/A");;
            $table->boolean('person_with_disability')->default(false);
            $table->text('disability_details')->nullable()->default("N/A");
            $table->boolean('pregnant')->default(false);
            $table->boolean('indigenous_community')->default(false);
            $table->text('indigenous_details')->nullable()->default("N/A");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
