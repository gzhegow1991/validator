<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Arr;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\Validation\ValidationInterface;


class KeysDiffAllRule extends AbstractRule
{
    const NAME = 'keys_diff_all';

    public static function message(array $conditions = []) : string
    {
        return 'validation.keys_diff_all';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        if (! isset($this->parameters[ 0 ])) {
            throw new LogicException(
                'The `parameters[0]` should be present, and known as `arrayToDiffAll`'
            );
        }

        $parameter0 = $this->parameters[ 0 ];
        $parameter1 = $this->parameters[ 1 ] ?? null;

        $valueArray = $value[ 0 ];

        if (! is_array($valueArray)) {
            return static::message();
        }

        if ([] === $valueArray) {
            return null;
        }

        $valueArrayKeys = array_keys($valueArray);

        $arrayToDiffAll = $parameter0;

        if (! is_array($arrayToDiffAll)) {
            throw new LogicException(
                [ 'The `arrayToDiffAll` should be array', $arrayToDiffAll ]
            );
        }

        if ([] === $arrayToDiffAll) {
            return null;
        }

        $cmpNative = true;
        $cmpNativeIsStrict = true;
        $cmpCustomFlagsMode = null;
        if (null !== $parameter1) {
            if (Lib::type()->int($int, $parameter1)) {
                $cmpNative = false;
                $cmpCustomFlagsMode = $int;

            } elseif (Lib::type()->string_not_empty($string, $parameter1)) {
                $cmpNativeIsStrict = ('strict' === $string);

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

        $status = true;

        foreach ( $arrayToDiffAll as $v ) {
            $found = false;
            foreach ( $valueArrayKeys as $vv ) {
                $bool = $cmpNative
                    ? ($cmpNativeIsStrict ? ($v === $vv) : ($v == $vv))
                    : (0 === $fnCmp($v, $vv));

                if ($bool) {
                    $found = true;

                    break;
                }
            }

            if ($found) {
                $status = false;

                break;
            }
        }

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
