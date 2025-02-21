<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstname', 'image_path', 'mi', 'lastname', 'suffix', 'email', 'phone', 'religion', 'sogie', 'birthdate',
        'address', 'highest_education', 'latest_company', 'present_position', 'status_employment',
        'last_employment_date', 'eligibility', 'person_with_disability', 'disability_details',
        'pregnant', 'indigenous_community', 'indigenous_details',
        'application_letter_path', 'personal_data_sheet_path', 'performance_rating_path',
        'eligibility_proof_path', 'transcript_path', 'employment_proof_path', 'training_certificates_path',
        'status'
    ];

    protected $casts = [
        'birthdate' => 'date',
        'last_employment_date' => 'date',
        'person_with_disability' => 'boolean',
        'pregnant' => 'boolean',
        'indigenous_community' => 'boolean',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(RegistrationDocument::class);
    }

    public function workPosition(): BelongsTo
    {
        return $this->belongsTo(WorkPosition::class)->withTrashed();
    }
}
