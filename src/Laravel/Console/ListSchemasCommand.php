<?php

namespace Schemator\Laravel\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Schemator\Contracts\ResponseDocument;

class ListSchemasCommand extends Command
{
    protected $signature = 'schemator:list';
    protected $description = 'List all routes with response schema (middleware schemator)';

    public function handle()
    {
        $routes = Route::getRoutes();

        $found = 0;

        foreach ($routes as $route) {
            $middlewares = $route->middleware();

            foreach ($middlewares as $middleware) {
                if (str_starts_with($middleware, 'schemator:')) {
                    $schemaClass = explode(':', $middleware)[1] ?? null;

                    if ($schemaClass && class_exists($schemaClass) && is_subclass_of($schemaClass, ResponseDocument::class)) {
                        /** @var ResponseDocument $document */
                        $document = new $schemaClass();

                        $this->line("\n🔹 <info>[{$route->uri()}]</info> - {$schemaClass}");
                        $this->line("  Methods: " . implode(', ', $route->methods()));
                        $this->line("  Schema:");
                        $this->line(json_encode($document->rules(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                        $found++;
                    }
                }
            }
        }

        if ($found === 0) {
            $this->warn('Nenhuma rota com middleware schemator encontrada.');
        }

        return Command::SUCCESS;
    }
}
