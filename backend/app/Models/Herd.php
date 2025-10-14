<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Herd extends Model
{
    use HasFactory;

    protected $fillable = [
        'species',
        'quantity',
        'purpose',
        'update_date',
        'property_id',
    ];

    protected $casts = [
        'update_date' => 'date',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
