<?php

namespace RafflesArgentina\ResourceController;

use Orchestra\Testbench\TestCase;

class ResourceControllerTest extends TestCase
{
    use BaseTest;

    function testIndexRoute()
    {
        factory(\RafflesArgentina\ResourceController\Models\User::class, 3)->create();

        $this->get('/test')
             ->assertViewIs('test.index')
             ->assertViewHas('items')
             ->assertStatus(200);

        $this->json('GET', '/test')
             ->assertStatus(200);
             //->assertJsonCount(3, 'data');
    }

    function testIndexRouteWithUseSoftDeletes()
    {
        $users = factory(\RafflesArgentina\ResourceController\Models\User::class, 3)->create();
        foreach ($users as $user) $user->delete();

        $this->get('/test2')
             ->assertViewIs('test.index')
             ->assertViewHas('items')
             ->assertStatus(200);

        $this->json('GET', '/test2')
             ->assertStatus(200);
             //->assertJsonCount(3, 'data');
    }

    function testCreateRoute()
    {
        $this->get('/test/create')
             ->assertViewIs('test.create')
             ->assertViewHas('model')
             ->assertStatus(200);

        $this->json('GET', '/test/create')
             ->assertStatus(404);
    }

    function testStoreRoute()
    {
        $this->post('/test', ['name' => 'Mario', 'email' => 'mario@raffles.com.ar', 'password' => bcrypt(str_random())])
             ->assertRedirect('/test')
             ->assertSessionHas('rafflesargentina.status.success');

        $this->json('POST', '/test', ['name' => 'Paula', 'email' => 'paula@raffles.com.ar', 'password' => bcrypt(str_random())])
             ->assertStatus(200);
    }

    function testShowRoute()
    {
        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();

        $this->get('/test/'.$user->id)
             ->assertViewIs('test.show')
             ->assertViewHas('model')
             ->assertStatus(200);

        $this->json('GET', '/test/'.$user->id)
             ->assertStatus(200);
    }

    function testShowRouteWithUseSoftDeletes()
    {
        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $user->delete();

        $this->get('/test2/'.$user->id)
             ->assertViewIs('test.show')
             ->assertViewHas('model')
             ->assertStatus(200);

        $this->json('GET', '/test2/'.$user->id)
             ->assertStatus(200);
    }

    function testShowRouteWithInexistentModel()
    {
        $this->get('/test/7')->assertStatus(404);

        $this->json('GET', '/test/7')->assertStatus(404);
    }

    function testEditRoute()
    {
        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();

        $this->get('/test/'.$user->id.'/edit')
             ->assertViewIs('test.edit')
             ->assertViewHas('model')
             ->assertStatus(200);

        $this->json('GET', '/test/'.$user->id.'/edit')
             ->assertStatus(404);
    }

    function testEditRouteWithUseSoftDeletes()
    {
        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $user->delete();

        $this->get('/test2/'.$user->id.'/edit')
             ->assertViewIs('test.edit')
             ->assertViewHas('model')
             ->assertStatus(200);

        $this->json('GET', '/test2/'.$user->id.'/edit')
             ->assertStatus(404);
    }

    function testEditRouteWithInexistentModel()
    {
        $this->get('/test/7/edit')->assertStatus(404);

        $this->json('GET', '/test/7/edit')->assertStatus(404);
    }

    function testUpdateRoute()
    {
        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();

        $this->put('/test/'.$user->id, ['name' => 'Mario', 'email' => 'mario@raffles.com.ar', 'password' => bcrypt(str_random())])
             ->assertRedirect('/test')
             ->assertStatus(302)
             ->assertSessionHas('rafflesargentina.status.success');

        $this->json('PUT', '/test/'.$user->id, ['name' => 'Mario', 'email' => 'mario@raffles.com.ar', 'password' => bcrypt(str_random())])
             ->assertStatus(200);
    }

    function testUpdateRouteWithUseSoftDeletes()
    {
        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $user->delete();

        $this->put('/test2/'.$user->id, ['name' => 'Mario', 'email' => 'mario@raffles.com.ar', 'password' => bcrypt(str_random())])
             ->assertRedirect('/test')
             ->assertStatus(302)
             ->assertSessionHas('rafflesargentina.status.success');

        $this->json('PUT', '/test2/'.$user->id, ['name' => 'Mario', 'email' => 'mario@raffles.com.ar', 'password' => bcrypt(str_random())])
             ->assertStatus(200);
    }

    function testUpdateRouteWithInexistentModel()
    {
        $this->put('/test/7')->assertStatus(404);

        $this->json('PUT', '/test/7')->assertStatus(404);
    }

    function testDestroyRoute()
    {
        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->delete('/test/'.$user->id)
             ->assertRedirect('/test')
             ->assertStatus(302);

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('DELETE', '/test/'.$user->id)
             ->assertStatus(200);
    }

    function testDestroyRouteWithUseSoftDeletes()
    {
        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $user->delete();

        $this->delete('/test2/'.$user->id)
             ->assertRedirect('/test')
             ->assertStatus(302);

        $this->json('DELETE', '/test2/'.$user->id)
             ->assertStatus(200);
    }

    function testDestroyRouteWithInexistentModel()
    {
        $this->delete('/test/7')->assertStatus(404);

        $this->json('DELETE', '/test/7')->assertStatus(404);
    }
}
