<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_id',
        'item_code',
        'item_name',
        'category',
        'result',
        'note',
    ];

    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }
}
