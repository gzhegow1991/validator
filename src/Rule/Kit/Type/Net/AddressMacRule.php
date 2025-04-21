<?php

namespace Gzhegow\Validator\Rule\Kit\Type\Net;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\Rule\Kit\Type\AbstractRuleType;


class AddressMacRule extends AbstractRuleType
{
    const NAME = 'address_mac';

    public static function message(array $conditions = []) : string
    {
        return 'validation.address_mac';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->address_mac($addressIp, $value[ 0 ]);

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
