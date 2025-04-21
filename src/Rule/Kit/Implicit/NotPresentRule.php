<?php

namespace Gzhegow\Validator\Rule\Kit\Implicit;

use Gzhegow\Validator\Validation\ValidationInterface;


class NotPresentRule extends AbstractRuleImplicit
{
    const NAME = 'not_present';

    public static function message(array $conditions = []) : string
    {
        return 'validation.not_present';
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

        return static::message();
    }
}
