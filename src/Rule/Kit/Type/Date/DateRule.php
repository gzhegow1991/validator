<?php

namespace Gzhegow\Validator\Rule\Kit\Type\Date;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\GenericRule;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\Rule\Kit\Type\AbstractRuleType;


class DateRule extends AbstractRuleType
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


    const NAME = 'date';

    public static function message(array $conditions = []) : string
    {
        return 'validation.date';
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

        $formats = null
            ?? (is_array($parameter0) ? $parameter0 : null)
            ?? (is_string($parameter0) ? [ $parameter0 ] : null)
            ?? [];

        if ([] === $formats) {
            throw new LogicException(
                'The `formats` should be non-empty string or array'
            );
        }

        $status = Lib::type()->date_formatted(
            $date,
            $value[ 0 ], $formats
        );

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
