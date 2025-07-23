<?php

namespace Gzhegow\Validator\Rule\Kit\Main;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class ListRule extends AbstractRule
{
    const NAME = 'list';

    public static function message(array $conditions = []) : string
    {
        return 'validation.list';
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
            ? Lib::type()->list_sorted($array, true)->isOk()
            : Lib::type()->list($array, true)->isOk();

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
