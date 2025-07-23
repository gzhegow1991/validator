<?php

namespace Gzhegow\Validator\Rule\Kit\Type;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;


class DecimalRule extends AbstractRuleType
{
    const NAME = 'decimal';

    public static function message(array $conditions = []) : string
    {
        return 'validation.decimal';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $parameter0 = $this->parameters[ 0 ] ?? null;

        $scale = intval($parameter0 ?? 0);

        $status = Lib::type()->decimal($value[ 0 ], $scale)->isOk();

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
