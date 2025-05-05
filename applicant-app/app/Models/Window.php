<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Window extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function currentTicket()
    {
        return $this->hasOne(Ticket::class)->where('status', 'serving')->latestOfMany();
    }
}
