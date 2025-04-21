<?php

namespace Gzhegow\Validator\Rule\Kit\Type;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;


class NumRule extends AbstractRuleType
{
    const NAME = 'num';

    public static function message(array $conditions = []) : string
    {
        return 'validation.num';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->num($result, $value[ 0 ]);

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
