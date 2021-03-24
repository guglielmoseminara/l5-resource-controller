<?php

namespace RafflesArgentina\ResourceController;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Str;

class WorksWithRelationsTest extends TestCase
{
    use BaseTest;

    /**
     * @coversNothing
     */
    function testStoreHasOne()
    {
        $fillable = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(Str::random()),
            'hasOneRelated' => [
                'a' => 'blah blah blah',
                'b' => 'blah blah blah',
                'c' => 'blah blah blah',
            ]
        ];

        $this->post('/test', $fillable)
            ->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue(!is_null($user->hasOneRelated));

        foreach ($fillable['hasOneRelated'] as $k => $v) {
            $this->assertTrue($user->hasOneRelated->{$k} == $v);
        }

        $user->forceDelete();

        $this->json('POST', '/test', $fillable)
            ->assertStatus(200)
            ->assertSessionHas('rafflesargentina.status.success');
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue(!is_null($user->hasOneRelated));
    }

    /**
     * @coversNothing
     */
    function testStoreMorphOne()
    {
        $fillable = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(Str::random()),
            'morphOneRelated' => [
                'a' => 'blah blah blah',
                'b' => 'blah blah blah',
                'c' => 'blah blah blah',
            ]
        ];

        $this->post('/test', $fillable)
            ->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue(!is_null($user->morphOneRelated));

        foreach ($fillable['morphOneRelated'] as $k => $v) {
            $this->assertTrue($user->morphOneRelated->{$k} == $v);
        }

        $user->forceDelete();

        $this->json('POST', '/test', $fillable)
            ->assertStatus(200)
            ->assertSessionHas('rafflesargentina.status.success');
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue(!is_null($user->morphOneRelated));

        foreach ($fillable['morphOneRelated'] as $k => $v) {
            $this->assertTrue($user->morphOneRelated->{$k} == $v);
        }
    }

    /**
     * @coversNothing
     */
    function testStoreBelongsTo()
    {
        $fillable = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(Str::random()),
            'belongsToRelated' => [
                'a' => 'blah blah blah',
                'b' => 'blah blah blah',
                'c' => 'blah blah blah',
            ]
        ];

        $this->post('/test', $fillable)
            ->assertRedirect('/test');

        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue(!is_null($user->belongsToRelated));

        foreach ($fillable['belongsToRelated'] as $k => $v) {
            $this->assertTrue($user->belongsToRelated->{$k} == $v);
        }

        $user->forceDelete();

        $this->json('POST', '/test', $fillable)->assertStatus(200);
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue(!is_null($user->belongsToRelated));

        foreach ($fillable['belongsToRelated'] as $k => $v) {
            $this->assertTrue($user->belongsToRelated->{$k} == $v);
        }
    }

    /**
     * @coversNothing
     */
    function testStoreHasMany()
    {
        $fillable = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(Str::random()),
            'hasManyRelated' => [
                '0' => [
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '1' => [
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '2' => [
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '3' => [
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '4' => [
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $this->post('/test', $fillable)->assertRedirect('/test');
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->hasManyRelated->count() === 5);

        foreach ($fillable['hasManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->hasManyRelated[$index]->{$k} == $v);
            }
        }

        $user->forceDelete();

        $this->json('POST', '/test', $fillable)->assertStatus(200);
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->hasManyRelated->count() === 5);

        foreach ($fillable['hasManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->hasManyRelated[$index]->{$k} == $v);
            }
        }
    }

    /**
     * @coversNothing
     */
    function testStoreMorphMany()
    {
        $fillable = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(Str::random()),
            'morphManyRelated' => [
                '0' => [
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '1' => [
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '2' => [
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '3' => [
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '4' => [
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $this->post('/test', $fillable)->assertRedirect('/test');
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->morphManyRelated->count() === 5);

        foreach ($fillable['morphManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->morphManyRelated[$index]->{$k} == $v);
            }
        }

        $user->forceDelete();

        $this->json('POST', '/test', $fillable)->assertStatus(200);
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->morphManyRelated->count() === 5);

        foreach ($fillable['morphManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->morphManyRelated[$index]->{$k} == $v);
            }
        }
    }

    /**
     * @coversNothing
     */
    function testStoreBelongsToManyExistent()
    {
        $related = factory(\RafflesArgentina\ResourceController\Models\Related::class, 5)->create();

        $existent = [];
        foreach ($related as $k => $model) {
            $existent[$k] = $model->getAttributes();
        }

        $fillable = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(Str::random()),
            'belongsToManyRelated' => [
                '0' => [
                    'id' => '1',
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '1' => [
                    'id' => '2',
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '2' => [
                    'id' => '3',
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '3' => [
                    'id' => '4',
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '4' => [
                    'id' => '5',
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $this->post('/test', $fillable)->assertRedirect('/test');
        $user = \RafflesArgentina\ResourceController\Models\User::with('belongsToManyRelated')->first();
        $this->assertTrue($user->belongsToManyRelated->count() === 5);

        foreach ($fillable['belongsToManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->belongsToManyRelated[$index]->{$k} == $v);
            }
        }

        $user->forceDelete();

        $this->json('POST', '/test', $fillable)->assertStatus(200);
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->belongsToManyRelated->count() === 5);

        foreach ($fillable['belongsToManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->belongsToManyRelated[$index]->{$k} == $v);
            }
        }
    }

    /**
     * @coversNothing
     */
    function testStoreBelongsToManyInexistent()
    {
        $fillable = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(Str::random()),
            'belongsToManyRelated' => [
                '0' => [
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '1' => [
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '2' => [
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '3' => [
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '4' => [
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $this->post('/test', $fillable)->assertRedirect('/test');
        $user = \RafflesArgentina\ResourceController\Models\User::with('belongsToManyRelated')->first();
        $this->assertTrue($user->belongsToManyRelated->count() === 5);

        foreach ($fillable['belongsToManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->belongsToManyRelated[$index]->{$k} == $v);
            }
        }

        $user->forceDelete();

        $this->json('POST', '/test', $fillable)->assertStatus(200);
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->belongsToManyRelated->count() === 5);

        foreach ($fillable['belongsToManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->belongsToManyRelated[$index]->{$k} == $v);
            }
        }
    }

    /**
     * @coversNothing
     */
    function testStoreMorphToManyExistent()
    {
        $related = factory(\RafflesArgentina\ResourceController\Models\Related::class, 5)->create();

        $existent = [];
        foreach ($related as $k => $model) {
            $existent[$k] = $model->getAttributes();
        }

        $fillable = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(Str::random()),
            'morphToManyRelated' => [
                '0' => [
                    'id' => '1',
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '1' => [
                    'id' => '2',
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '2' => [
                    'id' => '3',
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '3' => [
                    'id' => '4',
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '4' => [
                    'id' => '5',
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $this->post('/test', $fillable)->assertRedirect('/test');
        $user = \RafflesArgentina\ResourceController\Models\User::with('morphToManyRelated')->first();
        $this->assertTrue($user->morphToManyRelated->count() === 5);

        foreach ($fillable['morphToManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->morphToManyRelated[$index]->{$k} == $v);
            }
        }

        $user->forceDelete();

        $this->json('POST', '/test', $fillable)->assertStatus(200);
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->morphTOManyRelated->count() === 5);

        foreach ($fillable['morphToManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->morphToManyRelated[$index]->{$k} == $v);
            }
        }
    }

    /**
     * @coversNothing
     */
    function testStoreMorphToManyInexistent()
    {
        $fillable = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(Str::random()),
            'morphToManyRelated' => [
                '0' => [
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '1' => [
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '2' => [
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '3' => [
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '4' => [
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $this->post('/test', $fillable)->assertRedirect('/test');
        $user = \RafflesArgentina\ResourceController\Models\User::with('morphToManyRelated')->first();
        $this->assertTrue($user->morphToManyRelated->count() === 5);

        foreach ($fillable['morphToManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->morphToManyRelated[$index]->{$k} == $v);
            }
        }

        $user->forceDelete();

        $this->json('POST', '/test', $fillable)->assertStatus(200);
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->morphToManyRelated->count() === 5);

        foreach ($fillable['morphToManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->morphToManyRelated[$index]->{$k} == $v);
            }
        }
    }

    /**
     * @coversNothing
     */
    function testUpdateHasOne()
    {
        $fillable = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(Str::random()),
            'hasOneRelated' => [
                'a' => 'blah blah blah',
                'b' => 'blah blah blah',
                'c' => 'blah blah blah',
            ]
        ];

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->put('/test/1', $fillable)->assertRedirect('/test');
        $this->assertTrue(!is_null($user->hasOneRelated));

        $user->forceDelete();

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('PUT', '/test/2', $fillable)->assertStatus(200);
        $this->assertTrue(!is_null($user->hasOneRelated));
    }

    /**
     * @coversNothing
     */
    function testUpdateMorphOne()
    {
        $fillable = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(Str::random()),
            'morphOneRelated' => [
                'a' => 'blah blah blah',
                'b' => 'blah blah blah',
                'c' => 'blah blah blah',
            ]
        ];

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->put('/test/1', $fillable)->assertRedirect('/test');
        $this->assertTrue(!is_null($user->morphOneRelated));

        $user->forceDelete();

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('PUT', '/test/2', $fillable)->assertStatus(200);
        $this->assertTrue(!is_null($user->morphOneRelated));
    }

    /**
     * @coversNothing
     */
    function testUpdateBelongsTo()
    {
        $fillable = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(Str::random()),
            'belongsToRelated' => [
                'a' => 'blah blah blah',
                'b' => 'blah blah blah',
                'c' => 'blah blah blah',
            ]
        ];

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->put('/test/1', $fillable)->assertRedirect('/test');
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue(!is_null($user->belongsToRelated));

        $user->forceDelete();

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('PUT', '/test/2', $fillable)->assertStatus(200);
        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue(!is_null($user->belongsToRelated));
    }

    /**
     * @coversNothing
     */
    function testUpdateHasMany()
    {
        $fillable = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(Str::random()),
            'hasManyRelated' => [
                '0' => [
                    'id' => '1',
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '1' => [
                    'id' => '2',
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '2' => [
                    'id' => '3',
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '3' => [
                    'id' => '4',
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '4' => [
                    'id' => '5',
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->put('/test/1', $fillable)->assertRedirect('/test');
        $this->assertTrue($user->hasManyRelated->count() === 5);

        $user->forceDelete();

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('PUT', '/test/2', $fillable)->assertStatus(200);
        $this->assertTrue($user->hasManyRelated->count() === 5);
    }

    /**
     * @coversNothing
     */
    function testUpdateMorphMany()
    {
        $fillable = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(Str::random()),
            'morphManyRelated' => [
                '0' => [
                    'id' => '1',
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '1' => [
                    'id' => '2',
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '2' => [
                    'id' => '3',
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '3' => [
                    'id' => '4',
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '4' => [
                    'id' => '5',
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->put('/test/1', $fillable)->assertRedirect('/test');
        $this->assertTrue($user->morphManyRelated->count() === 5);

        $user->forceDelete();

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('PUT', '/test/2', $fillable)->assertStatus(200);
        $this->assertTrue($user->morphManyRelated->count() === 5);
    }

    /**
     * @coversNothing
     */
    function testUpdateBelongsToManyExistent()
    {
        $related = factory(\RafflesArgentina\ResourceController\Models\Related::class, 5)->create();

        $existent = [];
        foreach ($related as $k => $model) {
            $existent[$k] = $model->getAttributes();
        }

        $fillable = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(Str::random()),
            'belongsToManyRelated' => [
                '0' => [
                    'id' => '1',
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '1' => [
                    'id' => '2',
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '2' => [
                    'id' => '3',
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '3' => [
                    'id' => '4',
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '4' => [
                    'id' => '5',
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->put('/test/1', $fillable)->assertRedirect('/test');
        $this->assertTrue($user->belongsToManyRelated->count() === 5);

        foreach ($fillable['belongsToManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->belongsToManyRelated[$index]->{$k} == $v);
            }
        }

        $user->forceDelete();

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('PUT', '/test/2', $fillable)->assertStatus(200);
        $this->assertTrue($user->belongsToManyRelated->count() === 5);

        foreach ($fillable['belongsToManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->belongsToManyRelated[$index]->{$k} == $v);
            }
        }
    }

    /**
     * @coversNothing
     */
    function testUpdateBelongsToManyInexistent()
    {
        $fillable = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(Str::random()),
            'belongsToManyRelated' => [
                '0' => [
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '1' => [
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '2' => [
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '3' => [
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '4' => [
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->put('/test/1', $fillable)->assertRedirect('/test');
        $this->assertTrue($user->belongsToManyRelated->count() === 5);

        foreach ($fillable['belongsToManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->belongsToManyRelated[$index]->{$k} == $v);
            }
        }

        $user->forceDelete();

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('PUT', '/test/2', $fillable)->assertStatus(200);
        $this->assertTrue($user->belongsToManyRelated->count() === 5);

        foreach ($fillable['belongsToManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->belongsToManyRelated[$index]->{$k} == $v);
            }
        }
    }

    /**
     * @coversNothing
     */
    function testUpdateMorphToManyExistent()
    {
        $related = factory(\RafflesArgentina\ResourceController\Models\Related::class, 5)->create();

        $existent = [];
        foreach ($related as $k => $model) {
            $existent[$k] = $model->getAttributes();
        }

        $fillable = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(Str::random()),
            'morphToManyRelated' => [
                '0' => [
                    'id' => '1',
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '1' => [
                    'id' => '2',
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '2' => [
                    'id' => '3',
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '3' => [
                    'id' => '4',
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '4' => [
                    'id' => '5',
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->put('/test/1', $fillable)->assertRedirect('/test');
        $this->assertTrue($user->morphToManyRelated->count() === 5);

        foreach ($fillable['morphToManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->morphToManyRelated[$index]->{$k} == $v);
            }
        }

        $user->forceDelete();

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('PUT', '/test/2', $fillable)->assertStatus(200);
        $this->assertTrue($user->morphToManyRelated->count() === 5);

        foreach ($fillable['morphToManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->morphToManyRelated[$index]->{$k} == $v);
            }
        }
    }

    /**
     * @coversNothing
     */
    function testUpdateMorphToManyInexistent()
    {
        $fillable = [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => bcrypt(Str::random()),
            'morphToManyRelated' => [
                '0' => [
                    'a' => 'blah blah blah',
                    'b' => 'blah blah blah',
                    'c' => 'blah blah blah',
                ],
                '1' => [
                    'a' => 'bleh bleh bleh',
                    'b' => 'bleh bleh bleh',
                    'c' => 'bleh bleh bleh',
                ],
                '2' => [
                    'a' => 'blih blih blih',
                    'b' => 'blih blih blih',
                    'c' => 'blih blih blih',
                ],
                '3' => [
                    'a' => 'bloh bloh bloh',
                    'b' => 'bloh bloh bloh',
                    'c' => 'bloh bloh bloh',
                ],
                '4' => [
                    'a' => 'bluh bluh bluh',
                    'b' => 'bluh bluh bluh',
                    'c' => 'bluh bluh bluh',
                ]
            ]
        ];

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->put('/test/1', $fillable)->assertRedirect('/test');
        $this->assertTrue($user->morphToManyRelated->count() === 5);

        foreach ($fillable['morphToManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->morphToManyRelated[$index]->{$k} == $v);
            }
        }

        $user->forceDelete();

        $user = factory(\RafflesArgentina\ResourceController\Models\User::class)->create();
        $this->json('PUT', '/test/2', $fillable)->assertStatus(200);
        $this->assertTrue($user->morphToManyRelated->count() === 5);

        foreach ($fillable['morphToManyRelated'] as $index => $fields) {
            foreach ($fields as $k => $v) {
                $this->assertTrue($user->morphToManyRelated[$index]->{$k} == $v);
            }
        }
    }
}
