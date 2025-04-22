<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Cmp\Value;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\Validation\ValidationInterface;


class InsideRule extends AbstractRule
{
    const NAME = 'inside';

    public static function message(array $conditions = []) : string
    {
        return 'validation.inside';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        if (! isset($this->parameters[ 0 ])) {
            throw new LogicException(
                'The `parameters[0]` should be present, and known as `valueMin`'
            );
        }

        if (! isset($this->parameters[ 1 ])) {
            throw new LogicException(
                'The `parameters[1]` should be present, and known as `valueMax`'
            );
        }

        $parameter0 = $this->parameters[ 0 ];
        $parameter1 = $this->parameters[ 1 ];
        $parameter2 = $this->parameters[ 2 ] ?? null;

        $valueMin = $parameter0;
        $valueMax = $parameter1;

        $flagsMode = null;
        if (null !== $parameter2) {
            if (! Lib::type()->int($flagsMode, $parameter2)) {
                throw new LogicException(
                    [ 'The `parameters[2]` should be integer, and known as `flags`', $parameter2 ]
                );
            }
        }

        $fnCmp = Lib::cmp()->fnCompareValues(
            $flagsMode,
            _CMP_RESULT_NAN_RETURN
        );

        $status = $fnCmp($valueMin, $valueMax);

        if (! is_int($status)) {
            throw new LogicException(
                [ 'The `valueMin` and `valueMax` are incomparable', $valueMin, $valueMax ]
            );
        }

        if (0 <= $status) {
            throw new LogicException(
                [ 'The `valueMin` should be greater than `valueMax`', $valueMin, $valueMax ]
            );
        }

        $statusMin = $fnCmp($value[ 0 ], $valueMin);

        if (! is_int($statusMin)) {
            return static::message();
        }

        if (0 >= $statusMin) {
            return static::message();
        }

        $statusMax = $fnCmp($value[ 0 ], $valueMax);

        if (! is_int($statusMax)) {
            return static::message();
        }

        if (0 <= $statusMax) {
            return static::message();
        }

        return null;
    }
}
