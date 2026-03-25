<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class ToolInspection extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'inspection_no',
        'tool_id',
        'inspection_type',
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

    public function tool()
    {
        return $this->belongsTo(Tool::class, 'tool_id');
    }

    public function inspectedBy()
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function inspectionItems()
    {
        return $this->hasMany(ToolInspectionItem::class, 'inspection_id');
    }
}
