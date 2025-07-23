<?php

namespace Gzhegow\Validator\Rule\Kit\Type\Net;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\Rule\Kit\Type\AbstractRuleType;


class AddressIpV4Rule extends AbstractRuleType
{
    const NAME = 'address_ip_v4';

    public static function message(array $conditions = []) : string
    {
        return 'validation.address_ip_v4';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->address_ip_v4($value[ 0 ])->isOk([ &$addressIp ]);

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
