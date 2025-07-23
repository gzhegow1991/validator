<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Arr;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\Validation\ValidationInterface;


class KeysIntersectAnyRule extends AbstractRule
{
    const NAME = 'keys_intersect_any';

    public static function message(array $conditions = []) : string
    {
        return 'validation.keys_intersect_any';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        if (! isset($this->parameters[ 0 ])) {
            throw new LogicException(
                'The `parameters[0]` should be present, and known as `arrayToIntersectOne`'
            );
        }

        $parameter0 = $this->parameters[ 0 ];
        $parameter1 = $this->parameters[ 1 ] ?? null;

        $valueArray = $value[ 0 ];

        if (! is_array($valueArray)) {
            return static::message();
        }

        if ([] === $valueArray) {
            return static::message();
        }

        $valueKeysArray = array_keys($valueArray);

        $arrayToIntersectOne = $parameter0;

        if (! is_array($arrayToIntersectOne)) {
            throw new LogicException(
                [ 'The `arrayToIntersectOne` should be array', $arrayToIntersectOne ]
            );
        }

        if ([] === $arrayToIntersectOne) {
            return static::message();
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

        $status = false;

        foreach ( $arrayToIntersectOne as $v ) {
            foreach ( $valueKeysArray as $vv ) {
                $bool = $cmpNative
                    ? ($cmpNativeIsStrict ? ($v === $vv) : ($v == $vv))
                    : (0 === $fnCmp($v, $vv));

                if ($bool) {
                    $status = true;

                    break 2;
                }
            }
        }

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
