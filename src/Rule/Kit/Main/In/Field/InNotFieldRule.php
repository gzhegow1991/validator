<?php

namespace Gzhegow\Validator\Rule\Kit\Main\In\Field;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class InNotFieldRule extends AbstractRule
{
    const NAME = 'in_not_field';

    public static function message(array $conditions = []) : string
    {
        return 'validation.in_not_field';
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

        $fieldPath = $validation->fieldPathOrAbsolute($parameter0, $path);
        $fieldValue = $validation->get($fieldPath, [ $this ]);
        if ($this === $fieldValue) {
            return 'validation.fatal';
        }

        if (! is_array($fieldValue)) {
            return 'validation.fatal';
        }

        $cmpNative = true;
        $cmpNativeIsStrict = true;
        $cmpCustomFlagsMode = null;
        if (null !== $parameter1) {
            if (Lib::type()->int($int, $parameter1)) {
                $cmpNative = false;
                $cmpCustomFlagsMode = $int;

            } elseif (Lib::type()->userbool($bool, $parameter1)) {
                $cmpNativeIsStrict = $bool;

            } else {
                return 'validation.fatal';
            }
        }

        $fnCmp = null;
        if (! $cmpNative) {
            $cmpCustomFlagsMode = $cmpCustomFlagsMode ?? 0;

            $fnCmp = Lib::cmp()->fnCompareValues(
                $cmpCustomFlagsMode,
                _CMP_RESULT_NAN_RETURN
            );
        }

        $valueList = $fieldValue;
        $v = $value[ 0 ];

        $status = false;
        foreach ( $valueList as $vv ) {
            $bool = $cmpNative
                ? ($cmpNativeIsStrict ? ($v === $vv) : ($v == $vv))
                : (0 === $fnCmp($v, $vv));

            if ($bool) {
                $status = true;

                break;
            }
        }

        if ($status) {
            return static::message();
        }

        return null;
    }
}
