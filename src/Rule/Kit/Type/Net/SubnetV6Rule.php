<?php

namespace Gzhegow\Validator\Rule\Kit\Type\Net;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\Rule\Kit\Type\AbstractRuleType;


class SubnetV6Rule extends AbstractRuleType
{
    const NAME = 'subnet_v6';

    public static function message(array $conditions = []) : string
    {
        return 'validation.subnet_v6';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->subnet_v6($subnet, $value[ 0 ]);

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
