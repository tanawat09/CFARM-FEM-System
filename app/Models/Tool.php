<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Tool extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'type',
        'tool_code',
        'tool_name',
        'brand',
        'model',
        'serial_number',
        'location_id',
        'house',
        'zone',
        'purchase_date',
        'status',
        'qr_code',
        'note',
        'created_by',
        'next_inspection_date',
    ];

    protected $casts = [
        'purchase_date' => 'date',
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
        return $this->hasMany(ToolInspection::class, 'tool_id');
    }

    public function toolType()
    {
        return $this->belongsTo(ToolType::class, 'type', 'slug');
    }

    public function latestInspection()
    {
        return $this->hasOne(ToolInspection::class, 'tool_id')->latestOfMany('inspected_at');
    }

    public function getTypeNameAttribute()
    {
        $toolType = ToolType::where('slug', $this->type)->first();
        return $toolType ? $toolType->name : $this->type;
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
