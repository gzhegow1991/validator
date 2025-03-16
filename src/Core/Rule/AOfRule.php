<?php

namespace Gzhegow\Validator\Core\Rule;

use Gzhegow\Validator\Package\Illuminate\Validation\ValidatorInterface;


class AOfRule extends AbstractRule
{
    public function validate(
        $attribute, $value, $parameters,
        ValidatorInterface $validator
    ) : bool
    {
        return in_array($value, $parameters, true);
    }

    public function replace(
        $message, $attribute, $rule, $parameters,
        ValidatorInterface $validator
    ) : string
    {
        $join = implode(', ', $parameters);

        return str_replace(':join', $join, $message);
    }
}
