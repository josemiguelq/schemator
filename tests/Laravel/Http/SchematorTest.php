<?php

namespace Schemator\Tests\Laravel\Http;

use Illuminate\Support\Facades\Route;
use Schemator\Tests\Laravel\TestCase;

class SchematorTest extends TestCase
{
    protected function defineRoutes($router)
    {
        Route::middleware('schemator:'. UserDocument::class)
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

    public function test_json_response_is_validated()
    {
        $response = $this->get('/user');

        $response->assertOk();
        $response->assertJson([
            'id' => 1,
            'name' => 'José',
            'email' => 'jose@example.com',
        ]);
    }

    public function test_non_json_response_bypasses_validation()
    {
        $response = $this->get('/html');

        $response->assertOk();
        $this->assertStringContainsString('<h1>HTML</h1>', $response->getContent());
    }
}
