<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_no',
        'extinguisher_id',
        'inspected_by',
        'inspected_at',
        'overall_result',
        'remark',
        'next_inspection_date',
        'is_draft',
        'draft_saved_at',
        'weather_condition',
        'device_info',
    ];

    protected $casts = [
        'inspected_at' => 'datetime',
        'next_inspection_date' => 'date',
        'is_draft' => 'boolean',
        'draft_saved_at' => 'datetime',
    ];

    public function fireExtinguisher()
    {
        return $this->belongsTo(FireExtinguisher::class, 'extinguisher_id');
    }

    public function inspectedBy()
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function inspectionItems()
    {
        return $this->hasMany(InspectionItem::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
}
