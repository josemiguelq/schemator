<?php

namespace Schemator\Engine;

use Schemator\Contracts\ResponseDocument;

class OpenApiExport {
public function handle($routes)
    { foreach ($routes as $route) {
            foreach ($route->gatherMiddleware() as $middleware) {
                if (str_starts_with($middleware, 'schemator:')) {
                    [$prefix, $class] = explode(':', $middleware);

                    if (!class_exists($class)) {
                        throw new \Exception("Classe {$class} não encontrada.");
                        continue;
                    }

                    if (!is_subclass_of($class, ResponseDocument::class)) {
                        throw new \Exception("Classe {$class} não implementa ResponseDocument.");
                        continue;
                    }

                    /** @var ResponseDocument $instance */
                    $instance = new $class();
                    $rules = $instance->rules();

                    $responses = [];
                    foreach ($rules as $status => $schemaRules) {
                        $responses[(string) $status] = [
                            'description' => "HTTP $status response",
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => $this->convertToSwaggerProperties($schemaRules),
                                        'required' => $this->extractRequired($schemaRules),
                                    ],
                                ],
                            ],
                        ];
                    }

                    $schemas[$route->uri()] = [
                        strtolower($route->methods()[0]) => [
                            'responses' => $responses,
                        ],
                    ];
                }
            }
        }

        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'API Schemas',
                'version' => '1.0.0',
            ],
            'paths' => $schemas,
        ];
}
private function convertToSwaggerProperties(array $rules): array
    {
        $properties = [];
        foreach ($rules as $key => $rule) {
            $type = $rule['type'] ?? 'string';
            $property = ['type' => $type];

            if (($rule['format'] ?? null) === 'email') {
                $property['format'] = 'email';
            }

            $properties[$key] = $property;
        }

        return $properties;
    }

private function extractRequired(array $rules): array
    {
        $required = [];

        foreach ($rules as $key => $rule) {
            if (!isset($rule['required']) || $rule['required'] === true) {
                $required[] = $key;
            }
        }

        return $required;
    }
}