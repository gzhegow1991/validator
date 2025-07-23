<?php

namespace Gzhegow\Validator\Rule\Kit\Type\Date;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\Rule\Kit\Type\AbstractRuleType;


class TimezoneOffsetRule extends AbstractRuleType
{
    const NAME = 'timezone_offset';

    public static function message(array $conditions = []) : string
    {
        return 'validation.timezone_offset';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $allowedTimeZoneTypes = [ 1 ];

        $status = Lib::type()->timezone($value[ 0 ], $allowedTimeZoneTypes)->isOk();

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
