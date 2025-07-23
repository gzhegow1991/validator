<?php

namespace Gzhegow\Validator\Rule\Kit\Type\Social;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\Rule\Kit\Type\AbstractRuleType;


class PhoneRule extends AbstractRuleType
{
    const NAME = 'phone';

    public static function message(array $conditions = []) : string
    {
        return 'validation.phone';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->phone_non_fake($value[ 0 ])->isOk();

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
