<?php

namespace RafflesArgentina\ResourceController\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'location',
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

    public function morphOneFileUpload()
    {
        return $this->morphOne(Upload::class, 'uploadeable');
    }

    public function morphManyFileUploads()
    {
        return $this->morphMany(Upload::class, 'uploadeable');
    }

    public function morphToManyFileUploads()
    {
        return $this->morphToMany(Upload::class, 'uploadeable');
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

    public function morphOneRelated()
    {
        return $this->morphOne(Related::class, 'relatable');
    }

    public function morphManyRelated()
    {
        return $this->morphMany(Related::class, 'relatable');
    }

    public function morphToManyRelated()
    {
        return $this->morphToMany(Related::class, 'relatable');
    }
}
