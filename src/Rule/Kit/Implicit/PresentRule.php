<?php

namespace Gzhegow\Validator\Rule\Kit\Implicit;

use Gzhegow\Validator\Validation\ValidationInterface;


class PresentRule extends AbstractRuleImplicit
{
    const NAME = 'present';

    public static function message(array $conditions = []) : string
    {
        return 'validation.present';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) {
            // > missing - FAIL
            return static::message();
        }

        return null;
    }
}
