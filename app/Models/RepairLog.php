<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'repair_no',
        'extinguisher_id',
        'inspection_id',
        'problem',
        'action_taken',
        'repaired_by',
        'repair_cost',
        'vendor_name',
        'repaired_date',
        'completed_date',
        'status',
    ];

    protected $casts = [
        'repaired_date' => 'date',
        'completed_date' => 'date',
        'repair_cost' => 'decimal:2',
    ];

    public function fireExtinguisher()
    {
        return $this->belongsTo(FireExtinguisher::class, 'extinguisher_id');
    }

    public function repairedBy()
    {
        return $this->belongsTo(User::class, 'repaired_by');
    }

    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
}
