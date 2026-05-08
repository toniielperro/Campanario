<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'bell_sound_id',
        'sequence_id',
        'hora',
        'dias_semana',
        'frecuencia',
        'tipo',
        'fechas_especificas',
        'activo',
    ];

    protected $casts = [
        'dias_semana' => 'array',
        'fechas_especificas' => 'array',
        'activo' => 'boolean',
    ];

    public function bellSound()
    {
        return $this->belongsTo(BellSound::class);
    }

    public function sequence()
    {
        return $this->belongsTo(Sequence::class);
    }
}
