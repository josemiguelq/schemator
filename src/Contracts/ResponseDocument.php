<?php

namespace Schemator\Contracts;

use Respect\Validation\Validator;

interface ResponseDocument
{
    /**
     * ['key' => [type, required, example] ]
     */
    public function rules(): array;
}
