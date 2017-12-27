<?php

namespace RafflesArgentina\ResourceController\Repositories;

use Caffeinated\Repository\Repositories\EloquentRepository;

use RafflesArgentina\ResourceController\Models\User;

class TestRepository extends EloquentRepository
{
    public $model = User::class;

    protected $tag = ['user'];
}
