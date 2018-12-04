<?php

namespace RafflesArgentina\ResourceController;

class ApiTestController extends ResourceController
{
    protected $repository = Repositories\TestRepository::class;

    protected $resourceName = 'test3';
}
