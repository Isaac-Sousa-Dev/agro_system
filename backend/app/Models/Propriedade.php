<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Propriedade extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'municipio',
        'uf',
        'inscricao_estadual',
        'area_total',
        'produtor_id',
    ];

    protected $casts = [
        'area_total' => 'decimal:2',
    ];

    /**
     * Relacionamento com produtor
     */
    public function produtor(): BelongsTo
    {
        return $this->belongsTo(Produtor::class);
    }

    /**
     * Relacionamento com unidades de produção
     */
    public function unidadesProducao(): HasMany
    {
        return $this->hasMany(UnidadeProducao::class);
    }

    /**
     * Relacionamento com rebanhos
     */
    public function rebanhos(): HasMany
    {
        return $this->hasMany(Rebanho::class);
    }
}
