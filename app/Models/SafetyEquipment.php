<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class SafetyEquipment extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'safety_equipments';

    protected $fillable = [
        'type',
        'asset_code',
        'serial_number',
        'brand',
        'model',
        'location_id',
        'house',
        'zone',
        'install_date',
        'battery_replace_date',
        'status',
        'qr_code',
        'note',
        'created_by',
        'next_inspection_date',
    ];

    protected $casts = [
        'install_date' => 'date',
        'battery_replace_date' => 'date',
        'next_inspection_date' => 'date',
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
        return $this->hasMany(EquipmentInspection::class, 'equipment_id');
    }

    public function latestInspection()
    {
        return $this->hasOne(EquipmentInspection::class, 'equipment_id')->latestOfMany('inspected_at');
    }

    public function getTypeNameAttribute()
    {
        return match($this->type) {
            'emergency_light' => 'ไฟฉุกเฉิน',
            'eyewash_shower' => 'ที่ล้างตา/ฝักบัวฉุกเฉิน',
            default => $this->type,
        };
    }

    public function getStatusNameAttribute()
    {
        return match($this->status) {
            'active' => 'ใช้งานปกติ',
            'inactive' => 'ไม่ใช้งาน',
            'under_repair' => 'ซ่อมบำรุง',
            'disposed' => 'จำหน่ายแล้ว',
            default => $this->status,
        };
    }
}
