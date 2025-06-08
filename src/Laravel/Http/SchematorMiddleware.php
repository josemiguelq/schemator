<?php

namespace Schemator\Laravel\Http;

use Closure;
use Illuminate\Http\Request;
use Schemator\SchemaValidator;
use Schemator\Contracts\ResponseDocument;

class SchematorMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @param string $schemaClass
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $documentClass)
    {
        $response = $next($request);
        $body = json_decode($response->getContent(), true);
        // Só processa respostas JSON
        if (!$response->headers->contains('Content-Type', 'application/json')) {
            return $response;
        }

        if (!is_subclass_of($documentClass, ResponseDocument::class)) {
            throw new \InvalidArgumentException("{$documentClass} must implement ResponseDocument");
        }

        /** @var ResponseDocument $document */
        $document = new $documentClass();
        $rules = $document->rules();
        try {
            (new SchemaValidator())->validate($body, $rules, $response->status());
        } catch (ValidationException $e) {
            abort(500, 'Schema validation failed: ' . $e->getFullMessage());
        }

        return $response;
    }
}
