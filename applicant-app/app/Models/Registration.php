<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_initial',
        'last_name',
        'suffix',
        'email',
        'phone',
        'religion',
        'sogie',
        'birth_date',
        'address',
        'highest_education',
        'course_major',
        'latest_company',
        'present_position',
        'years_of_service',
        'last_employment_date',
        'eligibility',
        'person_with_disability',
        'disability_details',
        'pregnant',
        'indigenous_community',
        'indigenous_details',
        'image_path',
        'application_letter_path',
        'personal_data_sheet_path',
        'performance_rating_path',
        'eligibility_proof_path',
        'transcript_path',
        'employment_proof_path',
        'training_certificates_path',
        'status',
        'performance_rating_skipped',
        'employment_proof_skipped',
        'eligibility_proof_skipped',
        'remarks',
        'remarks_date'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'last_employment_date' => 'date',
        'remarks_date' => 'datetime',
        'person_with_disability' => 'boolean',
        'pregnant' => 'boolean',
        'indigenous_community' => 'boolean',
    ];

    public function setBirthDateAttribute($value): void
    {
        if ($value) {
            // Use Carbon without timezone conversion
            $this->attributes['birth_date'] = Carbon::parse($value)->format('Y-m-d');
        }
    }

    public function documents(): HasMany
    {
        return $this->hasMany(RegistrationDocument::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(WorkPosition::class, 'work_position_id');
    }
}
