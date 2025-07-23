<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Str;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class CtypeDigitRule extends AbstractRule
{
    const NAME = 'ctype_digit';

    public static function message(array $conditions = []) : string
    {
        return 'validation.ctype_digit';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->ctype_digit($value[ 0 ])->isOk();

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
