<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkPosition extends Model
{
    protected $fillable = ['name', 'description'];
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
}
