<?php

namespace Schemator\Laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Schemator\Laravel\Http\SchematorMiddleware;
use Schemator\Laravel\Console\ListSchemasCommand;

class SchematorServiceProvider extends ServiceProvider
{
    public function boot()
    {
         $this->app->make(Router::class)->aliasMiddleware('schemator', SchematorMiddleware::class);
    }

    public function register()
    {
     if ($this->app->runningInConsole()) {
            $this->commands([
                ListSchemasCommand::class,
                  \Schemator\Laravel\Console\ExportSchemasCommand::class,
            ]);
        }
    }
}

