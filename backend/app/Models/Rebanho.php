<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rebanho extends Model
{
    use HasFactory;

    protected $fillable = [
        'especie',
        'quantidade',
        'finalidade',
        'data_atualizacao',
        'propriedade_id',
    ];

    protected $casts = [
        'data_atualizacao' => 'date',
    ];

    /**
     * Relacionamento com propriedade
     */
    public function propriedade(): BelongsTo
    {
        return $this->belongsTo(Propriedade::class);
    }
}
