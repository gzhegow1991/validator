<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Str;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\Validation\ValidationInterface;


class StartsRule extends AbstractRule
{
    const NAME = 'starts';

    public static function message(array $conditions = []) : string
    {
        return 'validation.starts';
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

        if (! Lib::type()->string($stringToStarts, $parameter0)) {
            throw new LogicException(
                [ 'The `parameters[0]` should be string', $parameter0 ]
            );
        }

        if ('' === $stringToStarts) {
            return null;
        }

        $fnStrpos = Lib::str()->mb_func('strpos');

        if (0 !== $fnStrpos($string, $stringToStarts)) {
            return static::message();
        }

        return null;
    }
}
