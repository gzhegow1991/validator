<?php

namespace Gzhegow\Validator\Core\Rule;

use Gzhegow\Validator\Package\Illuminate\Validation\ValidatorInterface;


class InstanceIsClassRule extends AbstractRule
{
    public function validate(
        $attribute, $value, $parameters,
        ValidatorInterface $validator
    ) : bool
    {
        $_class = $parameters[ 0 ] ?? '';

        $_class = (string) $_class;

        if (! is_object($value)) {
            return false;
        }

        if ('' === $_class) {
            return false;
        }

        return get_class($value) === $_class;
    }

    public function replace(
        $message, $attribute, $rule, $parameters,
        ValidatorInterface $validator
    ) : string
    {
        $_class = $parameters[ 0 ] ?? '';

        $_class = (string) $_class;

        return str_replace(':class', $_class, $message);
    }
}
