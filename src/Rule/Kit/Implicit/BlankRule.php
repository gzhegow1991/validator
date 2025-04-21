<?php

namespace Gzhegow\Validator\Rule\Kit\Implicit;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;


class BlankRule extends AbstractRuleImplicit
{
    const NAME = 'blank';

    public static function message(array $conditions = []) : string
    {
        return 'validation.blank';
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

        if (! Lib::type()->is_blank($value[ 0 ])) {
            // > filled - FAIL
            return static::message();
        }

        return null;
    }
}
