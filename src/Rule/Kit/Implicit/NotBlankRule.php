<?php

namespace Gzhegow\Validator\Rule\Kit\Implicit;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;


class NotBlankRule extends AbstractRuleImplicit
{
    const NAME = 'not_blank';

    public static function message(array $conditions = []) : string
    {
        return 'validation.not_blank';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) {
            // > missing - OK
            return null;
        }

        if (! Lib::type()->blank($var, $value[ 0 ])) {
            // > filled - OK
            return null;
        }

        return static::message();
    }
}
