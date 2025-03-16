<?php

namespace Gzhegow\Validator\Core\Rule;

use Gzhegow\Validator\Package\Illuminate\Validation\ValidatorInterface;


abstract class AbstractRule implements RuleInterface
{
    public function validate(
        $attribute, $value, $parameters,
        ValidatorInterface $validator
    ) : bool
    {
        return false;
    }

    public function replace(
        $message, $attribute, $rule, $parameters,
        ValidatorInterface $validator
    ) : string
    {
        return $message;
    }
}
