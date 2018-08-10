<?php

namespace RafflesArgentina\ResourceController;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use Orchestra\Testbench\TestCase;

class WorksWithFileUploadsTest extends TestCase
{
    use BaseTest;

    /**
     * @covers ResourceController::store
     */
    function testPostHasOneFileUpload()
    {
        Storage::fake('uploads');

        $this->post(
            '/test', [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => str_random(),
            'hasOneFileUpload' => [
                UploadedFile::fake()->image('test.jpeg')
            ],
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue(!is_null($user->hasOneFileUpload));
    }

    /**
     * @coversNothing
     */
    function testPostMorphOneFileUpload()
    {
        Storage::fake('uploads');

        $this->post(
            '/test', [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => str_random(),
            'morphOneFileUpload' => [
                UploadedFile::fake()->image('test.jpeg')
            ],
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue(!is_null($user->morphOneFileUpload));
    }

    /**
     * @coversNothing
     */
    function testPostBelongsToFileUpload()
    {
        $user = \RafflesArgentina\ResourceController\Models\User::first();

        Storage::fake('uploads');

        $this->post(
            '/test', [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => str_random(),
            'belongsToFileUpload' => [
                UploadedFile::fake()->image('test.jpeg')
            ],
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue(!is_null($user->belongsToFileUpload));
    }

    /**
     * @coversNothing
     */
    function testPostHasManyFileUploads()
    {
        Storage::fake('uploads');

        $this->post(
            '/test', [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => str_random(),
            'hasManyFileUploads' => [
                '1' => UploadedFile::fake()->image('test.jpeg'),
                '2' => UploadedFile::fake()->create('document.pdf')
            ],
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->hasManyFileUploads->count() === 2);
    }

    /**
     * @coversNothing
     */
    function testPostMorphManyFileUploads()
    {
        $user = \RafflesArgentina\ResourceController\Models\User::first();

        Storage::fake('uploads');

        $this->post(
            '/test', [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => str_random(),
            'morphManyFileUploads' => [
                '1' => UploadedFile::fake()->image('test.jpeg'),
                '2' => UploadedFile::fake()->create('document.pdf')
            ],
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->morphManyFileUploads->count() === 2);
    }

    /**
     * @coversNothing
     */
    function testPostBelongsToManyFileUploads()
    {
        $user = \RafflesArgentina\ResourceController\Models\User::first();

        Storage::fake('uploads');

        $this->post(
            '/test', [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => str_random(),
            'belongsToManyFileUploads' => [
                '1' => UploadedFile::fake()->image('test.jpeg'),
                '2' => UploadedFile::fake()->create('document.pdf')
            ],
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->belongsToManyFileUploads->count() === 2);
    }

    /**
     * @coversNothing
     */
    function testPostMorphToManyFileUploads()
    {
        $user = \RafflesArgentina\ResourceController\Models\User::first();

        Storage::fake('uploads');

        $this->post(
            '/test', [
            'name' => 'Mario',
            'email' => 'mario@raffles.com.ar',
            'password' => str_random(),
            'morphToManyFileUploads' => [
                '1' => UploadedFile::fake()->image('test.jpeg'),
                '2' => UploadedFile::fake()->create('document.pdf')
            ],
            ]
        )->assertRedirect('/test')
            ->assertSessionHas('rafflesargentina.status.success');

        $user = \RafflesArgentina\ResourceController\Models\User::first();
        $this->assertTrue($user->morphToManyFileUploads->count() === 2);
    }
}
