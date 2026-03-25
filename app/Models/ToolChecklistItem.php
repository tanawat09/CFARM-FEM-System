<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'tool_type_id',
        'item_code',
        'category',
        'item_name',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function toolType()
    {
        return $this->belongsTo(ToolType::class);
    }
}
