<?php

namespace Gzhegow\Validator\Rule\Kit\Type\Date;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\Rule\Kit\Type\AbstractRuleType;


class TimezoneNamedRule extends AbstractRuleType
{
    const NAME = 'timezone_named';

    public static function message(array $conditions = []) : string
    {
        return 'validation.timezone_named';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $allowedTimeZoneTypes = [ 2, 3 ];

        $status = Lib::type()->timezone($value[ 0 ], $allowedTimeZoneTypes)->isOk();

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
