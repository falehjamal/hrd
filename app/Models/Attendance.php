<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    public const SOURCE_MANUAL = 'manual';

    public const SOURCE_GPS = 'gps';

    public const STATUS_PRESENT = 'present';

    public const STATUS_LATE = 'late';

    public const STATUS_ABSENT = 'absent';

    public const STATUS_HALF_DAY = 'half_day';

    public const STATUS_LEAVE = 'leave';

    protected $fillable = [
        'employee_id',
        'shift_id',
        'date',
        'check_in_at',
        'check_out_at',
        'check_in_photo_path',
        'check_out_photo_path',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_distance_m',
        'check_out_latitude',
        'check_out_longitude',
        'source',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'check_in_latitude' => 'decimal:7',
            'check_in_longitude' => 'decimal:7',
            'check_in_distance_m' => 'integer',
            'check_out_latitude' => 'decimal:7',
            'check_out_longitude' => 'decimal:7',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PRESENT => 'Hadir',
            self::STATUS_LATE => 'Terlambat',
            self::STATUS_ABSENT => 'Alpha',
            self::STATUS_HALF_DAY => 'Setengah Hari',
            self::STATUS_LEAVE => 'Cuti/Izin',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopePresentToday($query)
    {
        return $query->today()->whereIn('status', [self::STATUS_PRESENT, self::STATUS_LATE]);
    }
}
