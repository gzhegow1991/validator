<?php

namespace Gzhegow\Validator\Rule\Kit\Type;


class IntRule extends IntegerRule
{
    const NAME = 'int';

    public static function message(array $conditions = []) : string
    {
        return 'validation.int';
    }
}
