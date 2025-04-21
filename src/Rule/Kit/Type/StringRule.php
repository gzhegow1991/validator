<?php

namespace Gzhegow\Validator\Rule\Kit\Type;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;


class StringRule extends AbstractRuleType
{
    const NAME = 'string';

    public static function message(array $conditions = []) : string
    {
        return 'validation.string';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->string($result, $value[ 0 ]);

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
