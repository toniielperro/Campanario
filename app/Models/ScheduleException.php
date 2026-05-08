<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleException extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'start_time',
        'end_time',
        'fechas_especificas',
        'activo',
    ];

    protected $casts = [
        'fechas_especificas' => 'array',
        'activo' => 'boolean',
    ];
}
