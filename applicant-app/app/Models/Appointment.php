<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'contact', 'date', 'time', 'office_id', 'purpose', 'company_name', 'address'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * @throws \Exception
     */
    public static function createAppointment(array $data)
    {
        try {
            return self::create($data);
        } catch (\Exception $e) {
            \Log::error('Failed to create appointment: ' . $e->getMessage());
            throw $e;
        }
    }
}
