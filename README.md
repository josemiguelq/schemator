# Schemator

**Schemator** is a Laravel package for automatic validation of JSON response schemas and OpenAPI 3.0 documentation generation.  
Its goal is to ensure consistent API responses and avoid outdated documentation by generating OpenAPI specs directly from your route definitions.

---

## 🚀 Features

- ✅ JSON schema response validation
- 📄 Automatic OpenAPI 3.0 documentation generation
- 🎯 Laravel middleware integration
- 🔍 Ensures consistency between code and documentation

---

## 📦 Installation

```bash
composer require jose.miguel/schemator

## 🔧 Usage in Laravel
### 1. Create a Schema Document
Create a class implementing ResponseDocument and define the response structure by HTTP status code.

```php
use Schemator\Contracts\ResponseDocument;

class UserDocument implements ResponseDocument
{
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
}
```

### 2. Add Middleware to Your Route
Use the middleware in your route definition. The document class path is passed via schemator:.

```php
use Illuminate\Support\Facades\Route;

Route::middleware('schemator:' . \App\Wire\Out\UserDocument::class)
    ->get('/user', function () {
        return response()->json([
            'id' => 1,
            'name' => 'José',
            'email' => 'jose@example.com'
        ]);
    });
```    
## 🧪 Running Tests
Run all unit and feature tests using:
```
vendor/bin/phpunit
```

## 📤 Export OpenAPI Documentation

### Laravel
Generate an OpenAPI 3.0 specification file from your registered routes:

php artisan schemator:export
This command will scan all routes with the schemator: middleware and produce the schema definitions based on the associated ResponseDocument classes.

📁 Output Format Example
Example of exported paths from the /user route:
```json
{
  "/user": {
    "get": {
      "responses": {
        "200": {
          "description": "HTTP 200 response",
          "content": {
            "application/json": {
              "schema": {
                "type": "object",
                "properties": {
                  "id": { "type": "integer" },
                  "name": { "type": "string" },
                  "email": { "type": "string", "format": "email" }
                },
                "required": ["id"]
              }
            }
          }
        }
      }
    }
  }
}
```
## 🛠 Requirements
PHP 8.1+

Laravel 10+

PHPUnit (for tests)

## 📌 Limitations
Currently designed for use with Laravel only

Uses a custom JSON schema-like structure for defining response formats

## 🧑 Author
Created by José Miguel.

📃 License
MIT License. See LICENSE file for details.
