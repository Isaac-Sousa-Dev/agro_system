<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'crop_name',
        'total_area_ha',
        'geographic_coordinates',
        'property_id',
    ];

    protected $casts = [
        'total_area_ha' => 'decimal:2',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
