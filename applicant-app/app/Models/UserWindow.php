<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWindow extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'window_id',
        'assigned_at',
        'released_at',
        'is_active'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'released_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function window()
    {
        return $this->belongsTo(Window::class);
    }
}
