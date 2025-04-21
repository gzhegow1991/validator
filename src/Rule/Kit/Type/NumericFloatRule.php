<?php

namespace Gzhegow\Validator\Rule\Kit\Type;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;


class NumericFloatRule extends AbstractRuleType
{
    const NAME = 'numeric_float';

    public static function message(array $conditions = []) : string
    {
        return 'validation.numeric_float';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->numeric_float($result, $value[ 0 ]);

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
