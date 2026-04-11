<?php

namespace Gzhegow\Validator\Rule\Kit\Type;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;


class PercentNumericRule extends AbstractRuleType
{
    const NAME = 'percent_numeric';

    public static function message(array $conditions = []) : string
    {
        return 'validation.percent_numeric';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ( [] === $value ) return static::message();

        $status = Lib::type()->percent_numeric($value[0])->isOk();

        if ( ! $status ) {
            return static::message();
        }

        return null;
    }
}
