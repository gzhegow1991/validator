<?php

namespace Gzhegow\Validator\Rule\Kit\Type\Social;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\Rule\Kit\Type\AbstractRuleType;


class TelRealRule extends AbstractRuleType
{
    const NAME = 'tel_real';

    public static function message(array $conditions = []) : string
    {
        return 'validation.tel_real';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $parameter0 = $this->parameters[ 0 ] ?? null;

        $region = $parameter0 ?? '';

        if (! Lib::type()->tel_real($telRealString, $value[ 0 ], $region)) {
            return static::message();
        }

        return null;
    }
}
