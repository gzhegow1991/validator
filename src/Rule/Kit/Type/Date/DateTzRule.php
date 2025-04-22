<?php

namespace Gzhegow\Validator\Rule\Kit\Type\Date;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\GenericRule;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\Rule\Kit\Type\AbstractRuleType;


class DateTzRule extends AbstractRuleType
{
    public static function parse(string $ruleName, array $ruleArguments = []) : GenericRule
    {
        $ruleParameters[ 0 ] = $ruleArguments[ 0 ] ?? null;
        $ruleParameters[ 1 ] = $ruleArguments[ 1 ] ?? null;

        $ruleParameters[ 0 ] = is_string($ruleParameters[ 0 ])
            ? explode(',', $ruleParameters[ 0 ])
            : [];

        $ruleParameters[ 1 ] = is_string($ruleParameters[ 1 ])
            ? explode(',', $ruleParameters[ 1 ])
            : [];

        return GenericRule::fromClassAndParameters(
            static::class,
            [ 'parameters' => $ruleParameters ]
        );
    }


    const NAME = 'date_tz';

    public static function message(array $conditions = []) : string
    {
        return 'validation.date_tz';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        if (! isset($this->parameters[ 0 ])) {
            throw new LogicException(
                'The `parameters[0]` should be present, and known as `formats`'
            );
        }

        $parameter0 = $this->parameters[ 0 ];
        $parameter1 = $this->parameters[ 1 ] ?? null;

        $formats = null
            ?? (is_array($parameter0) ? $parameter0 : null)
            ?? (is_string($parameter0) ? [ $parameter0 ] : null)
            ?? [];

        if ([] === $formats) {
            throw new LogicException(
                'The `formats` should be non-empty string or array'
            );
        }

        $allowedTimeZoneTypes = null
            ?? (is_array($parameter1) ? $parameter1 : null)
            ?? (is_int($parameter1) ? [ $parameter1 ] : null)
            ?? null;

        $status = Lib::type()->date_tz_formatted(
            $dateTz,
            $formats, $value[ 0 ],
            $allowedTimeZoneTypes
        );

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
