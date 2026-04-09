<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class FireExtinguisher extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'asset_code',
        'serial_number',
        'type',
        'size',
        'size_unit',
        'brand',
        'model',
        'manufacture_date',
        'install_date',
        'expire_date',
        'last_refill_date',
        'next_refill_date',
        'next_inspection_date',
        'location_id',
        'house',
        'zone',
        'status',
        'map_x',
        'map_y',
        'qr_code',
        'qr_code_image',
        'note',
        'created_by',
    ];

    protected $casts = [
        'manufacture_date' => 'date',
        'install_date' => 'date',
        'expire_date' => 'date',
        'last_refill_date' => 'date',
        'next_refill_date' => 'date',
        'next_inspection_date' => 'date',
        'size' => 'decimal:2',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class, 'extinguisher_id');
    }

    public function repairLogs()
    {
        return $this->hasMany(RepairLog::class, 'extinguisher_id');
    }

    public function photos()
    {
        return $this->hasManyThrough(Photo::class, Inspection::class, 'extinguisher_id', 'inspection_id');
    }

    public static function getConfiguredExpireYears(): int
    {
        return max(1, SystemSetting::getInt('expire_years', 5));
    }

    public static function getWarningDaysBefore(): int
    {
        return max(1, SystemSetting::getInt('warning_days_before', 30));
    }

    public static function expireDateExpression(?int $expireYears = null): string
    {
        $expireYears = max(1, (int) ($expireYears ?? static::getConfiguredExpireYears()));

        return "DATE_ADD(manufacture_date, INTERVAL {$expireYears} YEAR)";
    }

    public function getConfiguredExpireDate(?int $expireYears = null): ?Carbon
    {
        if (!$this->manufacture_date) {
            return $this->expire_date;
        }

        return $this->manufacture_date->copy()->addYears(
            max(1, (int) ($expireYears ?? static::getConfiguredExpireYears()))
        );
    }

    public function scopeExpiringSoonByCurrentSetting(
        Builder $query,
        ?int $expireYears = null,
        ?int $warningDaysBefore = null
    ): Builder {
        $warningDaysBefore = max(1, (int) ($warningDaysBefore ?? static::getWarningDaysBefore()));
        $expireDateExpression = static::expireDateExpression($expireYears);

        return $query
            ->whereRaw("{$expireDateExpression} >= CURDATE()")
            ->whereRaw("{$expireDateExpression} <= DATE_ADD(CURDATE(), INTERVAL {$warningDaysBefore} DAY)");
    }

    public function scopeExpiredByCurrentSetting(Builder $query, ?int $expireYears = null): Builder
    {
        $expireDateExpression = static::expireDateExpression($expireYears);

        return $query->whereRaw("{$expireDateExpression} < CURDATE()");
    }
}
