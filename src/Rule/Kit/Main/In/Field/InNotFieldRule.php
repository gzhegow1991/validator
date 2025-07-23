<?php

namespace Gzhegow\Validator\Rule\Kit\Main\In\Field;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
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
            throw new LogicException(
                'The `parameters[0]` should be present, and known as `listField`'
            );
        }

        $parameter0 = $this->parameters[ 0 ];
        $parameter1 = $this->parameters[ 1 ] ?? null;

        $fieldPath = $validation->fieldpathOrAbsolute($parameter0, $path);
        $fieldValue = $validation->get($fieldPath, [ $this ]);
        if ($this === $fieldValue) {
            return static::message();
        }

        if (! is_array($fieldValue)) {
            return null;
        }

        $cmpNative = true;
        $cmpNativeIsStrict = true;
        $cmpCustomFlagsMode = null;
        if (null !== $parameter1) {
            $theType = Lib::type();

            if ($theType->int($parameter1)->isOk([ &$parameter1Int ])) {
                $cmpNative = false;
                $cmpCustomFlagsMode = $parameter1Int;

            } elseif ($theType->string_not_empty($parameter1)->isOk([ &$parameter1String ])) {
                $cmpNativeIsStrict = ('strict' === $parameter1String);

            } else {
                throw new LogicException(
                    [ 'The `parameters[1]` should be string "strict" or integer (`flags`)', $parameter1 ]
                );
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
