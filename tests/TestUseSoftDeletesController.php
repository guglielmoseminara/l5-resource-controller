<?php

namespace RafflesArgentina\ResourceController;

class TestUseSoftDeletesController extends ResourceController
{
    protected $repository = Repositories\TestRepository::class;

    protected $resourceName = 'test';

    protected $useSoftDeletes = true;
}
