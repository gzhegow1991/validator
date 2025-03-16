<?php

namespace Gzhegow\Validator\Core\Rule;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Package\Illuminate\Validation\ValidatorInterface;


class ListRule extends AbstractRule
{
    public function validate(
        $attribute, $value, $parameters,
        ValidatorInterface $validator
    ) : bool
    {
        $sorted = $parameters[ 0 ] ?? false;
        $sorted = (bool) $sorted;

        return $sorted
            ? Lib::arr()->type_list_sorted($result, $value)
            : Lib::arr()->type_list($result, $value);
    }
}
