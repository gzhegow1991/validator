<?php

namespace Gzhegow\Validator\Rule\Kit\Type\Date;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\GenericRule;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\Rule\Kit\Type\AbstractRuleType;


class TimezoneRule extends AbstractRuleType
{
    public static function parse(string $ruleName, array $ruleArguments = []) : GenericRule
    {
        $ruleParameters[ 0 ] = $ruleArguments[ 0 ] ?? null;

        $ruleParameters[ 0 ] = is_string($ruleParameters[ 0 ])
            ? explode(',', $ruleParameters[ 0 ])
            : [];

        return GenericRule::fromRuleClass(
            static::class,
            [ 'parameters' => $ruleParameters ]
        );
    }


    const NAME = 'timezone';

    public static function message(array $conditions = []) : string
    {
        return 'validation.timezone';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $parameter0 = $this->parameters[ 0 ] ?? null;

        $allowedTimeZoneTypes = null
            ?? (is_array($parameter0) ? $parameter0 : null)
            ?? (is_int($parameter0) ? [ $parameter0 ] : null)
            ?? null;

        $status = Lib::type()->timezone($dateTimeZone, $value[ 0 ], $allowedTimeZoneTypes);

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
