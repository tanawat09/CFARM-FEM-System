<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'extinguisher_id',
        'type',
        'sent_to',
        'sent_at',
        'channel',
        'is_read',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    public function fireExtinguisher()
    {
        return $this->belongsTo(FireExtinguisher::class, 'extinguisher_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'sent_to');
    }
}
