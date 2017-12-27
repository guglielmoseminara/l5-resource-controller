<?php

namespace RafflesArgentina\ResourceController;

use Orchestra\Testbench\TestCase;

use RafflesArgentina\ResourceController\Repositories\TestRepository;

class ResourceControllerTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testbench']);

        $this->artisan('migrate', ['--database' => 'testbench']);

        $this->withFactories(__DIR__.'/factories');

        \Route::group([
            'middleware' => [],
            'namespace'  => 'RafflesArgentina\ResourceController',
        ], function ($router) {
            $router->resource('test', 'TestController');
        });

        \View::addLocation(__DIR__.'/Resources/Views');
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    function testIndexMethod()
    {
        factory(\RafflesArgentina\ResourceController\Models\User::class, 3)->create();
        $this->get('/test')
             ->assertViewIs('test.index')
             ->assertViewHas('items')
             ->assertStatus(200);

        $this->json('GET', '/test')
             ->assertStatus(200);
    }

    function testCreateMethod()
    {
        $this->get('/test/create')
             ->assertViewIs('test.create')
             ->assertViewHas('model')
             ->assertStatus(200);

        $this->json('GET', '/test/create')
             ->assertStatus(404);
    }

    function testStoreMethod()
    {
        $this->post('/test', ['name' => 'Mario', 'email' => 'mario@raffles.com.ar', 'password' => bcrypt(str_random())])
             ->assertRedirect('/test');

        $this->json('POST', '/test', ['name' => 'Paula', 'email' => 'paula@raffles.com.ar', 'password' => bcrypt(str_random())])
             ->assertStatus(200);
    }

    function testShowMethod()
    {
        $test = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->get('/test/'.$test->id)
             ->assertViewIs('test.show')
             ->assertViewHas('model')
             ->assertStatus(200);

        $this->json('GET', '/test/'.$test->id)
             ->assertStatus(200);
    }

    function testEditMethod()
    {
        $test = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->get('/test/'.$test->id.'/edit')
             ->assertViewIs('test.edit')
             ->assertViewHas('model')
             ->assertStatus(200);

        $this->json('GET', '/test/'.$test->id.'/edit')
             ->assertStatus(404);
    }

    function testUpdateMethod()
    {
        $test = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->put('/test/'.$test->id, ['name' => 'Mario', 'email' => 'mario@raffles.com.ar', 'password' => bcrypt(str_random())])
             ->assertRedirect('/test')
             ->assertStatus(302);

        $this->json('PUT', '/test/'.$test->id, ['name' => 'Mario', 'email' => 'mario@raffles.com.ar', 'password' => bcrypt(str_random())])
             ->assertStatus(200);
    }

    function testDestroyMethod()
    {
        $test = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->delete('/test/'.$test->id)
             ->assertRedirect('/test')
             ->assertStatus(302);

        $test = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('DELETE', '/test/'.$test->id)
             ->assertStatus(200);
    }
}
