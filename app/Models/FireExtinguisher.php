<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FireExtinguisher extends Model
{
    use HasFactory, SoftDeletes;

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
}
