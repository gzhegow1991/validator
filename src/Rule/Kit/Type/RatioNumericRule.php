<?php

namespace Gzhegow\Validator\Rule\Kit\Type;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;


class RatioNumericRule extends AbstractRuleType
{
    const NAME = 'ratio_numeric';

    public static function message(array $conditions = []) : string
    {
        return 'validation.ratio_numeric';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ( [] === $value ) return static::message();

        $status = Lib::type()->ratio_decimal($value[0])->isOk();

        if ( ! $status ) {
            return static::message();
        }

        return null;
    }
}
