<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
