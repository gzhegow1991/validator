<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Obj;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\Validation\ValidationInterface;


class StructIsSubclassRule extends AbstractRule
{
    const NAME = 'struct_is_subclass';

    public static function message(array $conditions = []) : string
    {
        return 'validation.struct_is_subclass';
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

        $objectOrClassName = $value[ 0 ];

        if (! (is_string($objectOrClassName) || is_object($objectOrClassName))) {
            return static::message();
        }

        if (! Lib::type()->struct_exists($parameter0)->isOk([ &$instanceClass ])) {
            throw new LogicException(
                [ 'The `parameters[0]` should be existing struct', $parameter0 ]
            );
        }

        $status = is_subclass_of($objectOrClassName, $instanceClass, true);

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
