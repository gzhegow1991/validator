<?php

namespace Gzhegow\Validator\Rule\Kit\Main;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
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
            throw new LogicException(
                'The `parameters[0]` should be present, and known as `type`'
            );
        }

        $parameter0 = $this->parameters[ 0 ];

        if (! Lib::type()->string_not_empty($type, $parameter0)) {
            throw new LogicException(
                [ 'The `parameters[0]` should be non empty string', $parameter0 ]
            );
        }

        $status = ($type === gettype($value[ 0 ]));

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
