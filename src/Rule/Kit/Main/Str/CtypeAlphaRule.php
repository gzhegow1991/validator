<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Str;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class CtypeAlphaRule extends AbstractRule
{
    const NAME = 'ctype_alpha';

    public static function message(array $conditions = []) : string
    {
        return 'validation.ctype_alpha';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->ctype_alpha($result, $value[ 0 ]);

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
