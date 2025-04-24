<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Net;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\GenericRule;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\Validation\ValidationInterface;


class IpInSubnetsRule extends AbstractRule
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


    const NAME = 'ip_in_subnets';

    public static function message(array $conditions = []) : string
    {
        return 'validation.ip_in_subnets';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        if (! isset($this->parameters[ 0 ])) {
            throw new LogicException(
                'The `parameters[0]` should be present, and known as `subnets`'
            );
        }

        $parameter0 = $this->parameters[ 0 ];

        $theType = Lib::type();

        if (! $theType->address_ip($addressIp, $value[ 0 ])) {
            return static::message();
        }

        $subnets = null
            ?? (is_array($parameter0) ? $parameter0 : null)
            ?? ($theType->string_not_empty($string, $parameter0) ? [ $string ] : null)
            ?? [];

        $status = Lib::net()->is_ip_in_subnets($addressIp, $subnets);

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
