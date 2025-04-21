<?php

namespace Gzhegow\Validator\Rule\Kit\Main;

use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class ArrayRule extends AbstractRule
{
    const NAME = 'array';

    public static function message(array $conditions = []) : string
    {
        return 'validation.array';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = is_array($value[ 0 ]);

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
