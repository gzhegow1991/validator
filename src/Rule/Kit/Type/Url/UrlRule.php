<?php

namespace Gzhegow\Validator\Rule\Kit\Type\Url;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\Rule\Kit\Type\AbstractRuleType;


class UrlRule extends AbstractRuleType
{
    const NAME = 'url';

    public static function message(array $conditions = []) : string
    {
        return 'validation.url';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->url($result, $value[ 0 ]);

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
