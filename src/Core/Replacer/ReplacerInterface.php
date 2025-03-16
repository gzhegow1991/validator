<?php

namespace Gzhegow\Validator\Core\Replacer;

use Gzhegow\Validator\Package\Illuminate\Validation\ValidatorInterface;


interface ReplacerInterface
{
    public function replace(
        $message, $attribute, $rule, $parameters,
        ValidatorInterface $validator
    ) : string;
}
