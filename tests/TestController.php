<?php

namespace RafflesArgentina\ResourceController;

class TestController extends ResourceController
{
    protected $repository = Repositories\TestRepository::class;

    protected $resourceName = 'test';
}
