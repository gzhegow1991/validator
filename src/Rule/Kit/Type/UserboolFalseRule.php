<?php

namespace Gzhegow\Validator\Rule\Kit\Type;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;


class UserboolFalseRule extends AbstractRuleType
{
    const NAME = 'userbool_false';

    public static function message(array $conditions = []) : string
    {
        return 'validation.userbool_false';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->userbool($value[ 0 ])->isOk([ &$valueBool ]);

        if (! $status) {
            return static::message();
        }

        if (false !== $valueBool) {
            return static::message();
        }

        return null;
    }
}
