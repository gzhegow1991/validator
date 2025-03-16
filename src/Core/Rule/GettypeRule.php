<?php

namespace Gzhegow\Validator\Core\Rule;

use Gzhegow\Validator\Package\Illuminate\Validation\ValidatorInterface;


class GettypeRule extends AbstractRule
{
    public function validate(
        $attribute, $value, $parameters,
        ValidatorInterface $validator
    ) : bool
    {
        $_type = $parameters[ 0 ] ?? '';

        $_type = (string) $_type;

        return gettype($value) === $_type;
    }

    public function replace(
        $message, $attribute, $rule, $parameters,
        ValidatorInterface $validator
    ) : string
    {
        $_type = $parameters[ 0 ] ?? '';

        $_type = (string) $_type;

        return str_replace(':type', $_type, $message);
    }
}
