<?php

namespace Gzhegow\Validator\Rule\Kit\Main;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class GettypeRule extends AbstractRule
{
    const NAME = 'gettype';

    public static function message(array $conditions = []) : string
    {
        return 'validation.gettype';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        if (! isset($this->parameters[ 0 ])) {
            return 'validation.fatal';
        }

        $parameter0 = $this->parameters[ 0 ];

        if (! Lib::type()->string_not_empty($typeString, $parameter0)) {
            return 'validation.fatal';
        }

        $status = ($typeString === gettype($value[ 0 ]));

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
