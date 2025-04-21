<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Obj;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class IsOfClassRule extends AbstractRule
{
    const NAME = 'is_of_class';

    public static function message(array $conditions = []) : string
    {
        return 'validation.is_of_class';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $object = $value[ 0 ];

        if (! is_object($object)) {
            return static::message();
        }

        if (! isset($this->parameters[ 0 ])) {
            return 'validation.fatal';
        }

        $parameter0 = $this->parameters[ 0 ];

        if (! Lib::type()->string($instanceClass, $parameter0)) {
            return 'validation.fatal';
        }

        if (! (
            class_exists($instanceClass)
            || interface_exists($instanceClass)
            || ((PHP_VERSION_ID > 80100) && enum_exists($instanceClass))
        )) {
            return 'validation.fatal';
        }

        $status = get_class($object) === $instanceClass;

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
