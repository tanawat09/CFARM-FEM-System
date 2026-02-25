<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_code',
        'location_name',
        'building',
        'floor',
        'zone',
        'gps_lat',
        'gps_lng',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'gps_lat' => 'decimal:8',
        'gps_lng' => 'decimal:8',
    ];

    public function fireExtinguishers()
    {
        return $this->hasMany(FireExtinguisher::class);
    }

    public function inspections()
    {
        // hasManyThrough(
        //     RelatedModel::class,   // The far model we want to access
        //     ThroughModel::class,   // The intermediate model 
        //     'foreign_key_1',       // Foreign key on the through model matching this model's key (Location)
        //     'foreign_key_2',       // Foreign key on the related model matching the through model's key (FireExtinguisher)
        //     'local_key',           // Local key on this model (Location)
        //     'local_key_2'          // Local key on the intermediate model (FireExtinguisher)
        // )
        return $this->hasManyThrough(
            Inspection::class, 
            FireExtinguisher::class,
            'location_id', // Foreign key on fire_extinguishers table
            'extinguisher_id', // Foreign key on inspections table
            'id', // Local key on locations table
            'id' // Local key on fire_extinguishers table
        );
    }
}
