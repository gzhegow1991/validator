<?php

namespace Gzhegow\Validator\Core\Rule;


use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Package\Illuminate\Validation\ValidatorInterface;


class AIntRule extends AbstractRule
{
    public function validate(
        $attribute, $value, $parameters,
        ValidatorInterface $validator
    ) : bool
    {
        return Lib::type()->int($result, $value);
    }
}
