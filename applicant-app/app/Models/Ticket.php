<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'department',
        'issue_time',
        'call_time',
        'completion_time',
        'window_id',
        'status'
    ];

    protected $casts = [
        'issue_time' => 'datetime',
        'call_time' => 'datetime',
        'completion_time' => 'datetime',
    ];

    public static function where(string $string, string $string1)
    {
        // return xd ('status', 'waiting')
        return self::query()->where($string, $string1);
    }

    public function window(): BelongsTo
    {
        return $this->belongsTo(Window::class);
    }

    public static function generateTicketNumber()
    {
        $lastTicket = self::whereDate('created_at', today())->latest()->first();

        $nextNumber = 1;
        if ($lastTicket) {
            $nextNumber = intval($lastTicket->ticket_number) + 1;
        }

        // Format: 001, 002, etc.
        return str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function markAsServing(Window $window)
    {
        $this->window_id = $window->id;
        $this->status = 'serving';
        $this->call_time = now();
        $this->save();

        // For future WebSockets implementation
        // event(new TicketCalled($this));
    }

    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->completion_time = now();
        $this->save();
    }

    public function markAsSkipped()
    {
        $this->status = 'skipped';
        $this->save();
    }
}
