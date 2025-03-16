<?php

namespace Gzhegow\Validator\Core\Rule;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Package\Illuminate\Validation\ValidatorInterface;


class IndexRule extends AbstractRule
{
    public function validate(
        $attribute, $value, $parameters,
        ValidatorInterface $validator
    ) : bool
    {
        $isList = $parameters[ 0 ] ?? false;
        $isList = (bool) $isList;

        return $isList
            ? Lib::arr()->type_index_list($result, $value)
            : Lib::arr()->type_index_dict($result, $value);
    }
}
