<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('mi')->nullable();
            $table->string('lastname');
            $table->string('suffix')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->string('religion')->nullable();
            $table->string('sogie');
            $table->date('birthdate');
            $table->text('address');
            $table->string('highest_education');
            $table->string('latest_company')->nullable();
            $table->string('present_position')->nullable();
            $table->string('status_employment')->nullable();
            $table->date('last_employment_date')->nullable();
            $table->string('eligibility')->nullable();
            $table->boolean('person_with_disability')->default(false);
            $table->text('disability_details')->nullable();
            $table->boolean('pregnant')->default(false);
            $table->boolean('indigenous_community')->default(false);
            $table->text('indigenous_details')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('registrations');
    }
};
