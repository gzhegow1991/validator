<?php

namespace Gzhegow\Validator\Rule\Kit\Type\Social;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\Rule\Kit\Type\AbstractRuleType;


class TelRule extends AbstractRuleType
{
    const NAME = 'tel';

    public static function message(array $conditions = []) : string
    {
        return 'validation.tel';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->tel_non_fake($value[ 0 ])->isOk();

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
