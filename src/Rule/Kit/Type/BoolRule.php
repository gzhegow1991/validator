<?php

namespace Gzhegow\Validator\Rule\Kit\Type;


class BoolRule extends BooleanRule
{
    const NAME = 'bool';

    public static function message(array $conditions = []) : string
    {
        return 'validation.bool';
    }
}
