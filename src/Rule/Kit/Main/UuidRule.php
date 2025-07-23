<?php

namespace Gzhegow\Validator\Rule\Kit\Main;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class UuidRule extends AbstractRule
{
    const NAME = 'uuid';

    public static function message(array $conditions = []) : string
    {
        return 'validation.uuid';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $status = Lib::type()->uuid($value[ 0 ])->isOk();

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
