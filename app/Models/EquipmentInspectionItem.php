<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentInspectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_id',
        'item_code',
        'item_name',
        'category',
        'result',
    ];

    public function inspection()
    {
        return $this->belongsTo(EquipmentInspection::class, 'inspection_id');
    }
}
