<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_id',
        'repair_log_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'caption',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }

    public function repairLog()
    {
        return $this->belongsTo(RepairLog::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
