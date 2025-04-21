<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Arr;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class DiffAllRule extends AbstractRule
{
    const NAME = 'diff_all';

    public static function message(array $conditions = []) : string
    {
        return 'validation.diff_all';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $valueArray = $value[ 0 ];
        if (! is_array($valueArray)) {
            return static::message();
        }
        if ([] === $valueArray) {
            return null;
        }

        if (! isset($this->parameters[ 0 ])) {
            return 'validation.fatal';
        }

        $parameter0 = $this->parameters[ 0 ];
        $parameter1 = $this->parameters[ 1 ] ?? null;

        $arrayToDiffAll = $parameter0;
        if (! is_array($arrayToDiffAll)) {
            return 'validation.fatal';
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

        $status = true;

        foreach ( $arrayToDiffAll as $v ) {
            $found = false;
            foreach ( $valueArray as $vv ) {
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
