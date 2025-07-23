<?php

namespace Gzhegow\Validator\Rule\Kit\Type\Url;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\Rule\Kit\Type\AbstractRuleType;


class LinkRule extends AbstractRuleType
{
    const NAME = 'link';

    public static function message(array $conditions = []) : string
    {
        return 'validation.link';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->link($value[ 0 ])->isOk();

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
