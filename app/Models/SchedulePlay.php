<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchedulePlay extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'played_at',
    ];

    protected $dates = ['played_at'];

    protected $casts = [
        'played_at' => 'datetime',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
