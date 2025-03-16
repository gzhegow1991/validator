<?php

namespace Gzhegow\Validator\Core\Rule;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Package\Illuminate\Validation\ValidatorInterface;


class AUserboolRule extends AbstractRule
{
    public function validate(
        $attribute, $value, $parameters,
        ValidatorInterface $validator
    ) : bool
    {
        return Lib::type()->userbool($result, $value);
    }
}
