<?php

namespace Gzhegow\Validator\Core\Rule;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Package\Illuminate\Validation\ValidatorInterface;


class AUrlRule extends AbstractRule
{
    public function validate(
        $attribute, $value, $parameters,
        ValidatorInterface $validator
    ) : bool
    {
        return Lib::url()->type_url($result, $value);
    }
}
