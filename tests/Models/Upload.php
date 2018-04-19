<?php

namespace RafflesArgentina\ResourceController\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = [
        'user_id',
        'location',
        'uploadeable_id',
        'uploadeable_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->belongsToMany(Upload::class);
    }

    public function uploadeable()
    {
        return $this->morphTo();
    }
}
