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

        $status = Lib::type()->num($value[ 0 ])->isOk();

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
