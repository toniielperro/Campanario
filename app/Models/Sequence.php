<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sequence extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nombre','descripcion'];

    public function items()
    {
        return $this->hasMany(SequenceItem::class)->orderBy('orden');
    }
}
