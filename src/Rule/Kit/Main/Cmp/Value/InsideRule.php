<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Cmp\Value;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
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
            return 'validation.fatal';
        }

        $parameter0 = $this->parameters[ 0 ];
        $parameter1 = $this->parameters[ 1 ] ?? null;
        $parameter2 = $this->parameters[ 2 ] ?? null;

        $valueMin = $parameter0;
        $valueMax = $parameter1 ?? $valueMin;

        $flagsMode = null;
        if (null !== $parameter2) {
            $theType = Lib::type();

            if (! $theType->int($flagsMode, $parameter2)) {
                return 'validation.fatal';
            }
        }

        $fnCmp = Lib::cmp()->fnCompareValues(
            $flagsMode,
            _CMP_RESULT_NAN_RETURN
        );

        $status = $fnCmp($valueMin, $valueMax);

        if (! is_int($status)) {
            return 'validation.fatal';
        }

        if (0 <= $status) {
            return 'validation.fatal';
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
