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
            $table->string('disability_details')->nullable();
            $table->boolean('pregnant')->default(false);
            $table->boolean('indigenous_community')->default(false);
            $table->string('indigenous_details')->nullable();
            $table->string('application_letter_path');
            $table->string('personal_data_sheet_path');
            $table->string('performance_rating_path')->nullable();
            $table->string('eligibility_proof_path');
            $table->string('transcript_path');
            $table->string('employment_proof_path');
            $table->string('training_certificates_path');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('registrations');
    }
};
