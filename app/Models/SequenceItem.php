<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SequenceItem extends Model
{
    use HasFactory;

    protected $fillable = ['sequence_id','bell_sound_id','orden','interval_seconds'];

    protected $casts = [
        'interval_seconds' => 'float',
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
