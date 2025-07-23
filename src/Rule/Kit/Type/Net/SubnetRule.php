<?php

namespace Gzhegow\Validator\Rule\Kit\Type\Net;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\Rule\Kit\Type\AbstractRuleType;


class SubnetRule extends AbstractRuleType
{
    const NAME = 'subnet';

    public static function message(array $conditions = []) : string
    {
        return 'validation.subnet';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->subnet($value[ 0 ])->isOk();

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
