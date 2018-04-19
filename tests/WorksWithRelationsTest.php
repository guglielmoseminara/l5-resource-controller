<?php

namespace RafflesArgentina\ResourceController;

use Orchestra\Testbench\TestCase;

class WorksWithRelationsTest extends TestCase
{
    use BaseTest;

    function testStoreHasOne()
    {
        $fillables = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(str_random()),
            'hasOneRelated' => [
                'a' => 'blah blah blah',
                'b' => 'blah blah blah',
                'c' => 'blah blah blah',
            ]
        ];

        $this->post('/test', $fillables)
            ->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue(!is_null($user->hasOneRelated));

        $user->forceDelete();

        $this->json('POST', '/test', $fillables)
            ->assertStatus(200)
            ->assertSessionHas('rafflesargentina.status.success');
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue(!is_null($user->hasOneRelated));

        //dd($user->hasOneRelated->toJson());
    }

    function testStoreMorphOne()
    {
        $fillables = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(str_random()),
            'morphOneRelated' => [
                'a' => 'blah blah blah',
                'b' => 'blah blah blah',
                'c' => 'blah blah blah',
            ]
        ];

        $this->post('/test', $fillables)
            ->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue(!is_null($user->morphOneRelated));

        $user->forceDelete();

        $this->json('POST', '/test', $fillables)
            ->assertStatus(200)
            ->assertSessionHas('rafflesargentina.status.success');
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue(!is_null($user->morphOneRelated));

        //dd($user->hasOneRelated->toJson());
    }

    function testStoreBelongsTo()
    {
        $fillables = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(str_random()),
            'belongsToRelated' => [
                'a' => 'blah blah blah',
                'b' => 'blah blah blah',
                'c' => 'blah blah blah',
            ]
        ];

        $this->post('/test', $fillables)
            ->assertRedirect('/test');

        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue(!is_null($user->belongsToRelated));

        $user->forceDelete();

        $this->json('POST', '/test', $fillables)->assertStatus(200);
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue(!is_null($user->belongsToRelated));

        //dd($user->belongsToRelated->toJson());
    }

    function testStoreHasMany()
    {
        $fillables = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(str_random()),
            'hasManyRelated' => [
                '1' => [
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '2' => [
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '3' => [
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '4' => [
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '5' => [
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $this->post('/test', $fillables)->assertRedirect('/test');
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->hasManyRelated->count() === 5);

        $user->forceDelete();

        $this->json('POST', '/test', $fillables)->assertStatus(200);
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->hasManyRelated->count() === 5);

        //dd($user->hasManyRelated->toJson());
    }

    function testStoreMorphMany()
    {
        $fillables = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(str_random()),
            'morphManyRelated' => [
                '1' => [
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '2' => [
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '3' => [
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '4' => [
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '5' => [
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $this->post('/test', $fillables)->assertRedirect('/test');
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->morphManyRelated->count() === 5);

        $user->forceDelete();

        $this->json('POST', '/test', $fillables)->assertStatus(200);
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->morphManyRelated->count() === 5);

        //dd($user->hasManyRelated->toJson());
    }

    function testStoreBelongsToManyExistent()
    {
        $related = factory(\RafflesArgentina\ResourceController\Models\Related::class, 5)->create();

        $existent = [];
        foreach ($related as $id => $model) {
            $existent[$model->id] = $model->getAttributes();
        }

        $fillables = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(str_random()),
            'belongsToManyRelated' => $existent,
        ];

        $this->post('/test', $fillables)->assertRedirect('/test');
        $user = \RafflesArgentina\ResourceController\Models\User::with('belongsToManyRelated')->first();
        $this->assertTrue($user->belongsToManyRelated->count() === 5);

        $user->forceDelete();

        $this->json('POST', '/test', $fillables)->assertStatus(200);
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->belongsToManyRelated->count() === 5);

        //dd($user->belongsToManyRelated->toJson());
    }

    function testStoreMorphToManyExistent()
    {
        $related = factory(\RafflesArgentina\ResourceController\Models\Related::class, 5)->create();

        $existent = [];
        foreach ($related as $id => $model) {
            $existent[$model->id] = $model->getAttributes();
        }

        $fillables = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(str_random()),
            'morphToManyRelated' => $existent,
        ];

        $this->post('/test', $fillables)->assertRedirect('/test');

        $user = \RafflesArgentina\ResourceController\Models\User::with('morphToManyRelated')->first();

        $this->assertTrue($user->morphToManyRelated->count() === 5);

        $user->forceDelete();

        $this->json('POST', '/test', $fillables)->assertStatus(200);
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->morphTOManyRelated->count() === 5);

        //dd($user->belongsToManyRelated->toJson());
    }

    function testStoreBelongsToManyInexistent()
    {
        $fillables = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(str_random()),
            'belongsToManyRelated' => [
                '1' => [
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '2' => [
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '3' => [
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '4' => [
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '5' => [
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $this->post('/test', $fillables)->assertRedirect('/test');
        $user = \RafflesArgentina\ResourceController\Models\User::with('belongsToManyRelated')->first();
        $this->assertTrue($user->belongsToManyRelated->count() === 5);

        $user->forceDelete();

        $this->json('POST', '/test', $fillables)->assertStatus(200);
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->belongsToManyRelated->count() === 5);

        //dd($user->belongsToManyRelated->toJson());
    }

    function testStoreMorphToManyInexistent()
    {
        $fillables = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(str_random()),
            'morphToManyRelated' => [
                '1' => [
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '2' => [
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '3' => [
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '4' => [
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '5' => [
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $this->post('/test', $fillables)->assertRedirect('/test');
        $user = \RafflesArgentina\ResourceController\Models\User::with('morphToManyRelated')->first();
        $this->assertTrue($user->morphToManyRelated->count() === 5);

        $user->forceDelete();

        $this->json('POST', '/test', $fillables)->assertStatus(200);
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->morphToManyRelated->count() === 5);

        //dd($user->belongsToManyRelated->toJson());
    }

    function testUpdateHasOne()
    {
        $fillables = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(str_random()),
            'hasOneRelated' => [
                'a' => 'blah blah blah',
                'b' => 'blah blah blah',
                'c' => 'blah blah blah',
            ]
        ];

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->put('/test/1', $fillables)->assertRedirect('/test');
        $this->assertTrue(!is_null($user->hasOneRelated));

        $user->forceDelete();

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('PUT', '/test/2', $fillables)->assertStatus(200);
        $this->assertTrue(!is_null($user->hasOneRelated));
    }

    function testUpdateMorphOne()
    {
        $fillables = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(str_random()),
            'morphOneRelated' => [
                'a' => 'blah blah blah',
                'b' => 'blah blah blah',
                'c' => 'blah blah blah',
            ]
        ];

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->put('/test/1', $fillables)->assertRedirect('/test');
        $this->assertTrue(!is_null($user->morphOneRelated));

        $user->forceDelete();

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('PUT', '/test/2', $fillables)->assertStatus(200);
        $this->assertTrue(!is_null($user->morphOneRelated));
    }

    function testUpdateBelongsTo()
    {
        $fillables = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(str_random()),
            'belongsToRelated' => [
                'a' => 'blah blah blah',
                'b' => 'blah blah blah',
                'c' => 'blah blah blah',
            ]
        ];

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->put('/test/1', $fillables)->assertRedirect('/test');
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue(!is_null($user->belongsToRelated));

        $user->forceDelete();

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('PUT', '/test/2', $fillables)->assertStatus(200);
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue(!is_null($user->belongsToRelated));
    }

    function testUpdateHasMany()
    {
        $fillables = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(str_random()),
            'hasManyRelated' => [
                '1' => [
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '2' => [
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '3' => [
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '4' => [
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '5' => [
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->put('/test/1', $fillables)->assertRedirect('/test');
        $this->assertTrue($user->hasManyRelated->count() === 5);

        $user->forceDelete();

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('PUT', '/test/2', $fillables)->assertStatus(200);
        $this->assertTrue($user->hasManyRelated->count() === 5);
    }

    function testUpdateMorphMany()
    {
        $fillables = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(str_random()),
            'morphManyRelated' => [
                '1' => [
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '2' => [
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '3' => [
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '4' => [
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '5' => [
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->put('/test/1', $fillables)->assertRedirect('/test');
        $this->assertTrue($user->morphManyRelated->count() === 5);

        $user->forceDelete();

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('PUT', '/test/2', $fillables)->assertStatus(200);
        $this->assertTrue($user->morphManyRelated->count() === 5);
    }

    function testUpdateBelongsToManyExistent()
    {
        $related = factory(\RafflesArgentina\ResourceController\Models\Related::class, 5)->create();

        $existent = [];
        foreach ($related as $id => $model) {
            $existent[$model->id] = $model->getAttributes();
        }

        $fillables = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(str_random()),
            'belongsToManyRelated' => $existent
        ];

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->put('/test/1', $fillables)->assertRedirect('/test');
        $this->assertTrue($user->belongsToManyRelated->count() === 5);

        $user->forceDelete();

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('PUT','/test/2', $fillables)->assertStatus(200);
        $this->assertTrue($user->belongsToManyRelated->count() === 5);
    }

    function testUpdateMorphToManyExistent()
    {
        $related = factory(\RafflesArgentina\ResourceController\Models\Related::class, 5)->create();

        $existent = [];
        foreach ($related as $id => $model) {
            $existent[$model->id] = $model->getAttributes();
        }

        $fillables = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(str_random()),
            'morphToManyRelated' => $existent
        ];

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->put('/test/1', $fillables)->assertRedirect('/test');
        $this->assertTrue($user->morphToManyRelated->count() === 5);

        $user->forceDelete();

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('PUT','/test/2', $fillables)->assertStatus(200);
        $this->assertTrue($user->morphToManyRelated->count() === 5);
    }

    function testUpdateBelongsToManyInexistent()
    {
        $fillables = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(str_random()),
            'belongsToManyRelated' => [
                '1' => [
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '2' => [
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '3' => [
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '4' => [
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '5' => [
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->put('/test/1', $fillables)->assertRedirect('/test');
        $this->assertTrue($user->belongsToManyRelated->count() === 5);

        $user->forceDelete();

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('PUT','/test/2', $fillables)->assertStatus(200);
        $this->assertTrue($user->belongsToManyRelated->count() === 5);
    }

    function testUpdateMorphToManyInexistent()
    {
        $fillables = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(str_random()),
            'morphToManyRelated' => [
                '1' => [
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '2' => [
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '3' => [
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '4' => [
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '5' => [
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->put('/test/1', $fillables)->assertRedirect('/test');
        $this->assertTrue($user->morphToManyRelated->count() === 5);

        $user->forceDelete();

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('PUT','/test/2', $fillables)->assertStatus(200);
        $this->assertTrue($user->morphToManyRelated->count() === 5);
    }
}
