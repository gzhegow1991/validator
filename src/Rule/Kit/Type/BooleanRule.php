<?php

namespace Gzhegow\Validator\Rule\Kit\Type;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;


class BooleanRule extends AbstractRuleType
{
    const NAME = 'boolean';

    public static function message(array $conditions = []) : string
    {
        return 'validation.boolean';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->bool($value[ 0 ])->isOk();

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
