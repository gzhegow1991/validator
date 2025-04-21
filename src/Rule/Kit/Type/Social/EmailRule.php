<?php

namespace Gzhegow\Validator\Rule\Kit\Type\Social;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\Rule\Kit\Type\AbstractRuleType;


class EmailRule extends AbstractRuleType
{
    const NAME = 'email';

    public static function message(array $conditions = []) : string
    {
        return 'validation.email';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $parameter0 = $this->parameters[ 0 ] ?? null;

        $filters = null
            ?? (is_array($parameter0) ? $parameter0 : null)
            ?? (is_string($parameter0) ? [ $parameter0 ] : null)
            ?? null;

        if (! Lib::type()->email_non_fake($emailString, $value[ 0 ], $filters)) {
            return static::message();
        }

        return null;
    }
}
