<?php

namespace Gzhegow\Validator\Exception\Runtime;

use Gzhegow\Validator\Exception\RuntimeException;
use Gzhegow\Validator\Validation\ValidationInterface;


class ValidationException extends RuntimeException
{
    /**
     * @var ValidationInterface
     */
    protected $validation;


    public function __construct(ValidationInterface $validation, ...$throwableArgs)
    {
        $this->validation = $validation;

        parent::__construct($throwableArgs);
    }


    public function getValidation() : ValidationInterface
    {
        return $this->validation;
    }
}
