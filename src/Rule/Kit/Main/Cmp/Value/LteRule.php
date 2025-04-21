<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Cmp\Value;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class LteRule extends AbstractRule
{
    const NAME = 'lte';

    public static function message(array $conditions = []) : string
    {
        return 'validation.lte';
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

        $valueModel = $parameter0;

        $flagsMode = null;
        if (null !== $parameter1) {
            $theType = Lib::type();

            if (! $theType->int($flagsMode, $parameter1)) {
                return 'validation.fatal';
            }
        }

        $fnCmp = Lib::cmp()->fnCompareValues(
            $flagsMode,
            _CMP_RESULT_NAN_RETURN
        );

        $status = $fnCmp($value[ 0 ], $valueModel);

        if (! is_int($status)) {
            return static::message();
        }

        if (0 < $status) {
            return static::message();
        }

        return null;
    }
}
