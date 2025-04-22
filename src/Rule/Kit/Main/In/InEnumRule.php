<?php

namespace Gzhegow\Validator\Rule\Kit\Main\In;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\Validation\ValidationInterface;


class InEnumRule extends AbstractRule
{
    const NAME = 'in_enum';

    public static function message(array $conditions = []) : string
    {
        return 'validation.in_enum';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        if (! isset($this->parameters[ 0 ])) {
            throw new LogicException(
                'The `parameters[0]` should be present, and known as `enum`'
            );
        }

        $parameter0 = $this->parameters[ 0 ];

        $enumItem = $value[ 0 ];

        $theType = Lib::type();

        if (! $theType->struct_enum($enumClass, $parameter0)) {
            throw new LogicException(
                [ 'The `parameters[0]` should be enum class or object', $parameter0 ]
            );
        }

        $status = $theType->enum_case($enumItemObject, $enumItem, $enumClass);

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
