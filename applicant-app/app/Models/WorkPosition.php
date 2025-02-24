<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkPosition extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'description'];
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
}
