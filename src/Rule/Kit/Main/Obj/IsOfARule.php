<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Obj;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\Validation\ValidationInterface;


class IsOfARule extends AbstractRule
{
    const NAME = 'is_of_a';

    public static function message(array $conditions = []) : string
    {
        return 'validation.is_of_a';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        if (! isset($this->parameters[ 0 ])) {
            throw new LogicException(
                'The `parameters[0]` should be present, and known as `class`'
            );
        }

        $parameter0 = $this->parameters[ 0 ];

        $object = $value[ 0 ];

        if (! is_object($object)) {
            return static::message();
        }

        if (! Lib::type()->struct_exists($instanceClass, $parameter0)) {
            throw new LogicException(
                [ 'The `parameters[0]` should be existing struct', $parameter0 ]
            );
        }

        $status = is_a($object, $instanceClass, false);

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
