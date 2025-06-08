<?php

namespace Schemator\Tests\Laravel\Unit\Engine;

use PHPUnit\Framework\TestCase;
use Schemator\Engine\OpenApiExport;
use Schemator\Contracts\ResponseDocument;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;

class OpenApiExportTest extends TestCase
{
    public function test_it_generates_openapi_spec_from_routes()
    {
        // Simulando a classe do documento
        $mockDocument = new class implements ResponseDocument {
            public function rules(): array
            {
                return [
                    200 => [
                        'id' => ['type' => 'integer', 'required' => true],
                        'name' => ['type' => 'string'],
                        'email' => ['type' => 'string', 'format' => 'email']
                    ],
                    400 => [
                        'error' => ['type' => 'string']
                    ]
                ];
            }
        };

        // Nome totalmente qualificado da classe fictícia
        $mockDocumentClass = get_class($mockDocument);

        // Simulando uma rota com o middleware 'schemator:classe'
        $route = $this->createMock(Route::class);
        $route->method('uri')->willReturn('/user');
        $route->method('methods')->willReturn(['GET']);
        $route->method('gatherMiddleware')->willReturn(["schemator:".\Schemator\Tests\Laravel\Http\UserDocument::class]);

        // Injetando a classe mock manualmente para evitar erro de autoload
        eval("class_alias('" . addslashes($mockDocumentClass) . "', '\\FakeUserDocument');");

        // Executar
        $exporter = new OpenApiExport();
        $spec = $exporter->handle([$route]);

        // Asserções
        $this->assertArrayHasKey('openapi', $spec);
        $this->assertEquals('3.0.0', $spec['openapi']);

        $this->assertArrayHasKey('/user', $spec['paths']);
        $this->assertArrayHasKey('get', $spec['paths']['/user']);
        $this->assertArrayHasKey('200', $spec['paths']['/user']['get']['responses']);
        $this->assertArrayHasKey('400', $spec['paths']['/user']['get']['responses']);

        $this->assertEquals('integer', $spec['paths']['/user']['get']['responses']['200']['content']['application/json']['schema']['properties']['id']['type']);
        $this->assertContains('id', $spec['paths']['/user']['get']['responses']['200']['content']['application/json']['schema']['required']);
    }
}
