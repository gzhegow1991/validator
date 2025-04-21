<?php

namespace Gzhegow\Validator\Rule\Kit\Type;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;


class UserboolTrueRule extends AbstractRuleType
{
    const NAME = 'userbool_true';

    public static function message(array $conditions = []) : string
    {
        return 'validation.userbool_true';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->userbool($bool, $value[ 0 ]);

        if (! $status) {
            return static::message();
        }

        if (true !== $bool) {
            return static::message();
        }

        return null;
    }
}
