<?php

namespace Gzhegow\Validator;

use Gzhegow\Validator\Validation\Validation;


interface ValidatorInterface
{
    public function new() : Validation;
}
