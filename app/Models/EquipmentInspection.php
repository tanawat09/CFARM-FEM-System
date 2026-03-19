<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class EquipmentInspection extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'inspection_no',
        'equipment_id',
        'inspected_by',
        'inspected_at',
        'overall_result',
        'remark',
        'next_inspection_date',
    ];

    protected $casts = [
        'inspected_at' => 'datetime',
        'next_inspection_date' => 'date',
    ];

    public function equipment()
    {
        return $this->belongsTo(SafetyEquipment::class, 'equipment_id');
    }

    public function inspectedBy()
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function inspectionItems()
    {
        return $this->hasMany(EquipmentInspectionItem::class, 'inspection_id');
    }
}
