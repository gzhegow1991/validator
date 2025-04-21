<?php

namespace Gzhegow\Validator\Rule\Kit\Main\In;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\GenericRule;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class InNotRule extends AbstractRule
{
    public static function parse(string $ruleName, array $ruleArguments = []) : GenericRule
    {
        $ruleParameters[ 0 ] = $ruleArguments[ 0 ] ?? null;

        $ruleParameters[ 0 ] = is_string($ruleParameters[ 0 ])
            ? explode(',', $ruleParameters[ 0 ])
            : [];

        return GenericRule::fromClassAndParameters(
            static::class,
            [ 'parameters' => $ruleParameters ]
        );
    }


    const NAME = 'in_not';

    public static function message(array $conditions = []) : string
    {
        return 'validation.in_not';
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

        if (! is_array($parameter0)) {
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

        $valueList = $parameter0;
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
