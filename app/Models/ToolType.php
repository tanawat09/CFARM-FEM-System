<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolType extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'icon',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function checklistItems()
    {
        return $this->hasMany(ToolChecklistItem::class)->orderBy('sort_order');
    }

    public function activeChecklistItems()
    {
        return $this->hasMany(ToolChecklistItem::class)->where('is_active', true)->orderBy('sort_order');
    }

    public function tools()
    {
        return $this->hasMany(Tool::class, 'type', 'slug');
    }
}
