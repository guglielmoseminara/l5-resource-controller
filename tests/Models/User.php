<?php

namespace RafflesArgentina\ResourceController\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'upload_id',
        'related_id',
    ];

    public function hasOneFileUpload()
    {
        return $this->hasOne(Upload::class);
    }

    public function belongsToFileUpload()
    {
        return $this->belongsTo(Upload::class, 'upload_id');
    }

    public function hasManyFileUploads()
    {
        return $this->hasMany(Upload::class);
    }

    public function belongsToManyFileUploads()
    {
        return $this->belongsToMany(Upload::class, 'upload_user', 'upload_id', 'user_id');
    }

    public function hasOneRelated()
    {
        return $this->hasOne(Related::class);
    }

    public function belongsToRelated()
    {
        return $this->belongsTo(Related::class, 'related_id');
    }

    public function hasManyRelated()
    {
        return $this->hasMany(Related::class);
    }

    public function belongsToManyRelated()
    {
        return $this->belongsToMany(Related::class);
    }
}
