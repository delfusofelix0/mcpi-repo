<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'birthdate' => 'date',
        'last_employment_date' => 'date',
        'person_with_disability' => 'boolean',
        'pregnant' => 'boolean',
        'indigenous_community' => 'boolean',
    ];
}
