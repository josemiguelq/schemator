<?php

namespace Schemator\Tests\Laravel;

use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Schemator\Laravel\SchematorServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            SchematorServiceProvider::class,
        ];
    }

    protected function defineRoutes($router)
    {
        Route::middleware('schemator:Schemator/Tests/Feature/UserDocument')
            ->get('/user', function () {
                return response()->json([
                    'id' => 1,
                    'name' => 'José',
                    'email' => 'jose@example.com',
                ]);
            });

        Route::get('/html', function () {
            return response('<h1>HTML</h1>');
        });
    }
}

