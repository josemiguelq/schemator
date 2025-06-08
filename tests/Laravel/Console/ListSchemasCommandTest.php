<?php

namespace Schemator\Tests\Laravel\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Schemator\Contracts\ResponseDocument;
use Schemator\Tests\Laravel\TestCase;

class ListSchemasCommandTest extends TestCase
{
    protected function setUp(): void
        {
            parent::setUp();

            // Define rota dentro do setUp (importante)
            Route::middleware("schemator:" . UserDocument::class)
                ->get('/user', function () {
                    return response()->json([
                        'id' => 1,
                        'name' => 'Teste',
                        'email' => 'teste@example.com',
                    ]);
                });
        }
    protected function getPackageProviders($app)
    {
        return [
            \Schemator\Laravel\SchematorServiceProvider::class,
        ];
    }

    /** @test */
    public function it_lists_routes_with_schemator_middleware()
    {
        $this->artisan('schemator:list')
            ->expectsOutputToContain(UserDocument::class)
            ->expectsOutputToContain('"id"')
            ->assertExitCode(0);

            $this->artisan('schemator:list')
                        ->expectsOutputToContain('user')
                        ->expectsOutputToContain('"required": true')
                        ->assertExitCode(0);
    }

    /** @test */
    public function it_shows_message_if_no_routes_found()
    {
       Route::setRoutes(new \Illuminate\Routing\RouteCollection());

           $this->artisan('schemator:list')
               ->expectsOutput('Nenhuma rota com middleware schemator encontrada.')
               ->assertExitCode(0);
    }
}
