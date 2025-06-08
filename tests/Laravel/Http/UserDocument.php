<?php
namespace Schemator\Tests\Laravel\Http;

use Schemator\Contracts\ResponseDocument;

class UserDocument implements ResponseDocument
{
    public function rules(): array
    {
        return  [200 => [
        'id' => ['type' => 'integer', 'required' => true],
        'name' => ['type' => 'string'],
        'email' => ['type' => 'string', 'format' => 'email']
       ],
       400 => ['error' => ['type' => 'string']]];
    }
}