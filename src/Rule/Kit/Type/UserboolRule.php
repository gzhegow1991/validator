<?php

namespace Gzhegow\Validator\Rule\Kit\Type;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;


class UserboolRule extends AbstractRuleType
{
    const NAME = 'userbool';

    public static function message(array $conditions = []) : string
    {
        return 'validation.userbool';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->userbool($value[ 0 ])->isOk();

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
