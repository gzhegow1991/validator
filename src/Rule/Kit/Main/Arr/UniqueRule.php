<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Arr;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\Validation\ValidationInterface;


class UniqueRule extends AbstractRule
{
    const NAME = 'unique';

    public static function message(array $conditions = []) : string
    {
        return 'validation.unique';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $parameter0 = $this->parameters[ 0 ] ?? null;

        $array = $value[ 0 ];

        if (! is_array($array)) {
            return static::message();
        }

        $cmpNative = true;
        $cmpNativeIsStrict = true;
        $cmpCustomFlagsMode = null;
        if (null !== $parameter0) {
            if (Lib::type()->int($int, $parameter0)) {
                $cmpNative = false;
                $cmpCustomFlagsMode = $int;

            } elseif (Lib::type()->userbool($bool, $parameter0)) {
                $cmpNativeIsStrict = $bool;

            } elseif (Lib::type()->string_not_empty($string, $parameter0)) {
                $cmpNativeIsStrict = ('strict' === $string);

            } else {
                throw new LogicException(
                    [
                        'The `parameters[0]` should be string "strict", integer (`flags`), userbool (`isStrict`)',
                        $parameter0,
                    ]
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

        $seen = [];
        foreach ( $array as $v ) {
            foreach ( $seen as $vv ) {
                $bool = $cmpNative
                    ? ($cmpNativeIsStrict ? ($v === $vv) : ($v == $vv))
                    : (0 === $fnCmp($v, $vv));

                if ($bool) {
                    $status = false;

                    break 2;
                }
            }

            $seen[] = $v;
        }

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
