<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Office extends Model
{
    use HasFactory;

    public mixed $is_available;
    protected $fillable = [
        'name',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function appointment(): hasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
