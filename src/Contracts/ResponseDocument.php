<?php

namespace Schemator\Contracts;

use Respect\Validation\Validator;

interface ResponseDocument
{
    /**
     * ['key' => [type, required, example] ]
     */
    // TODO support default schema for errors
    public function rules(): array;
}
