<?php

namespace Gzhegow\Validator\Core\Rule;

use Gzhegow\Validator\Package\Illuminate\Validation\ValidatorInterface;


interface RuleInterface
{
    public function validate(
        $attribute, $value, $parameters,
        ValidatorInterface $validator
    ) : bool;

    public function replace(
        $message, $attribute, $rule, $parameters,
        ValidatorInterface $validator
    ) : string;
}
