<?php

namespace Gzhegow\Validator\Rule\Kit\Type;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;


class PercentRule extends AbstractRuleType
{
    const NAME = 'percent';

    public static function message(array $conditions = []) : string
    {
        return 'validation.percent';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ( [] === $value ) return static::message();

        $status = Lib::type()->percent($value[0])->isOk();

        if ( ! $status ) {
            return static::message();
        }

        return null;
    }
}
