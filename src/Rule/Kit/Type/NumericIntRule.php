<?php

namespace Gzhegow\Validator\Rule\Kit\Type;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;


class NumericIntRule extends AbstractRuleType
{
    const NAME = 'numeric_int';

    public static function message(array $conditions = []) : string
    {
        return 'validation.numeric_int';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->numeric_int($value[ 0 ])->isOk();

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
