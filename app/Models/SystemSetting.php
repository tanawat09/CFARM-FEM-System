<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class SystemSetting extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'key',
        'value',
        'description',
    ];
}
