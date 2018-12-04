<?php

namespace RafflesArgentina\ResourceController;

class ApiTestUseSoftDeletesController extends ResourceController
{
    protected $repository = Repositories\TestRepository::class;

    protected $resourceName = 'test4';

    protected $useSoftDeletes = true;
}
