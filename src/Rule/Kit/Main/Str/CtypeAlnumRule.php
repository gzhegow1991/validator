<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Str;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class CtypeAlnumRule extends AbstractRule
{
    const NAME = 'ctype_alnum';

    public static function message(array $conditions = []) : string
    {
        return 'validation.ctype_alnum';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->ctype_alnum($value[ 0 ])->isOk();

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
