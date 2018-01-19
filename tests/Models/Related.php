<?php

namespace RafflesArgentina\ResourceController\Models;

use Illuminate\Database\Eloquent\Model;

class Related extends Model
{
    protected $table = 'related';

    protected $fillable = [
        'a',
        'b',
        'c',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
