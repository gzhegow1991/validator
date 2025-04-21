<?php

namespace Gzhegow\Validator\Rule\Kit\Type\Date;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\Rule\Kit\Type\AbstractRuleType;


class IntervalRule extends AbstractRuleType
{
    const NAME = 'interval';

    public static function message(array $conditions = []) : string
    {
        return 'validation.interval';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->interval_duration($dateInterval, $value[ 0 ]);

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
