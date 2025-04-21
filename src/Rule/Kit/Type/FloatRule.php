<?php

namespace Gzhegow\Validator\Rule\Kit\Type;


class FloatRule extends DoubleRule
{
    const NAME = 'float';

    public static function message(array $conditions = []) : string
    {
        return 'validation.float';
    }
}
