<?php

namespace Schemator;

use Respect\Validation\Validator;
use Respect\Validation\Rules\Key;
use Respect\Validation\Exceptions\ValidationException;

class SchemaValidator
{
    public function validate(array $data, array $schemas, int $statusCode): void
    {
        $schema = $schemas[$statusCode] ?? [];
        foreach ($schema as $field => $rules) {
            $isRequired = $rules['required'] ?? false;

            if ($isRequired && !array_key_exists($field, $data)) {
                throw new \RuntimeException("Missing required field: '{$field}'");
            }

            if (!array_key_exists($field, $data)) {
                continue; // Campo opcional não presente — ok
            }

            $value = $data[$field];

            $expectedType = $rules['type'] ?? null;
            if ($expectedType && !$this->validateType($value, $expectedType)) {
                throw new \RuntimeException("Field '{$field}' must be of type '{$expectedType}'");
            }

            if (!empty($rules['format'])) {
                if ($rules['format'] === 'email' && !$this->isValidEmail($value)) {
                    throw new \RuntimeException("Field '{$field}' must be a valid email");
                }
            }
        }
    }

    private function validateType($value, string $type): bool
    {
        return match ($type) {
            'string' => is_string($value),
            'integer' => is_int($value),
            'boolean' => is_bool($value),
            'array' => is_array($value),
            'object' => is_array($value), // arrays associativos podem representar objetos
            default => false,
        };
    }

    private function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
