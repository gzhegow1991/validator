<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Str;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\Validation\ValidationInterface;


class ContainsRule extends AbstractRule
{
    const NAME = 'contains';

    public static function message(array $conditions = []) : string
    {
        return 'validation.contains';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        if (! isset($this->parameters[ 0 ])) {
            throw new LogicException(
                'The `parameters[0]` should be present, and known as `needle`'
            );
        }

        $parameter0 = $this->parameters[ 0 ];

        if (! Lib::type()->string_not_empty($string, $value[ 0 ])) {
            return static::message();
        }

        if (! Lib::type()->string($needle, $parameter0)) {
            throw new LogicException(
                [ 'The `parameters[0]` should be string', $needle ]
            );
        }

        if ('' === $needle) {
            return null;
        }

        $fnStrpos = Lib::str()->mb_func('strpos');

        if (false === $fnStrpos($string, $needle)) {
            return static::message();
        }

        return null;
    }
}
