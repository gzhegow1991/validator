<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Cmp\Value;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\Validation\ValidationInterface;


class LtRule extends AbstractRule
{
    const NAME = 'lt';

    public static function message(array $conditions = []) : string
    {
        return 'validation.lt';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        if (! isset($this->parameters[ 0 ])) {
            throw new LogicException(
                'The `parameters[0]` should be present, and known as `valueLt`'
            );
        }

        $parameter0 = $this->parameters[ 0 ];
        $parameter1 = $this->parameters[ 1 ] ?? null;

        $valueLt = $parameter0;

        $flagsMode = null;
        if (null !== $parameter1) {
            $theType = Lib::type();

            if (! $theType->int($flagsMode, $parameter1)) {
                throw new LogicException(
                    [ 'The `parameters[1]` should be integer, and known as `flags`', $parameter1 ]
                );
            }
        }

        $fnCmp = Lib::cmp()->fnCompareValues(
            $flagsMode,
            _CMP_RESULT_NAN_RETURN
        );

        $status = $fnCmp($value[ 0 ], $valueLt);

        if (! is_int($status)) {
            return static::message();
        }

        if (0 <= $status) {
            return static::message();
        }

        return null;
    }
}
