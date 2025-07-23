<?php

namespace Gzhegow\Validator\Rule\Kit\Type\Social;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\Rule\Kit\Type\AbstractRuleType;


class PhoneRealRule extends AbstractRuleType
{
    const NAME = 'phone_real';

    public static function message(array $conditions = []) : string
    {
        return 'validation.phone_real';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $parameter0 = $this->parameters[ 0 ] ?? null;

        $region = $parameter0 ?? '';

        $status = Lib::type()->phone_real($value[ 0 ], $region)->isOk();

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
