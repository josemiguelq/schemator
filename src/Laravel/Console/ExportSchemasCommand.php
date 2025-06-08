<?php

namespace Schemator\Laravel\Console;

use Illuminate\Console\Command;
use Schemator\Engine\OpenApiExport;
use Illuminate\Support\Facades\Route;
use Schemator\Contracts\ResponseDocument;

class ExportSchemasCommand extends Command
{
    protected $signature = 'schemator:export {--output=swagger.json}';
    protected $description = 'Exporta todos os schemas registrados no middleware Schemator em formato Swagger (OpenAPI 3.0).';

    public function handle()
    {
        $routes = Route::getRoutes();
        $schemas = [];
        $swagger = (new OpenApiExport())->handle($routes);
        $outputFile = base_path($this->option('output'));
        file_put_contents($outputFile, json_encode($swagger, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->info("Swagger exportado com sucesso em: {$outputFile}");
    }



}
