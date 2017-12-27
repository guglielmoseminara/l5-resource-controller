<?php

namespace RafflesArgentina\ResourceController\Repositories;

use Caffeinated\Repository\Repositories\EloquentRepository;

use \App\User;

class TestRepository extends EloquentRepository
{
    public $model = User::class;

    protected $tag = [
        'created'  => 'UserCreated',
        'updated'  => 'UserUpdated',
        'deleted'  => 'UserDeleted',
    ];
}
