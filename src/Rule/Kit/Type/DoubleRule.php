<?php

namespace Gzhegow\Validator\Rule\Kit\Type;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;


class DoubleRule extends AbstractRuleType
{
    const NAME = 'double';

    public static function message(array $conditions = []) : string
    {
        return 'validation.double';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->float($result, $value[ 0 ]);

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
