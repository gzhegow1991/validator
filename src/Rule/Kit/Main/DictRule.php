<?php

namespace Gzhegow\Validator\Rule\Kit\Main;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class DictRule extends AbstractRule
{
    const NAME = 'dict';

    public static function message(array $conditions = []) : string
    {
        return 'validation.dict';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $parameter0 = $this->parameters[ 0 ] ?? null;

        $array = $value[ 0 ];

        if (! is_array($array)) {
            return static::message();
        }

        $isSorted = boolval($parameter0 ?? false);

        $status = $isSorted
            ? Lib::type()->dict_sorted($result, $array, true)
            : Lib::type()->dict($result, $array, true);

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
