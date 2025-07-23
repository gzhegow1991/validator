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

        $theType = Lib::type();

        if (! $theType->string_not_empty($value[ 0 ])->isOk([ &$valueStringNotEmpty ])) {
            return static::message();
        }

        if (! $theType->string($parameter0)->isOk([ &$stringStarts ])) {
            throw new LogicException(
                [ 'The `parameters[0]` should be string', $parameter0 ]
            );
        }

        if ('' === $stringStarts) {
            return null;
        }

        $fnStrpos = Lib::str()->mb_func('strpos');

        if (0 !== $fnStrpos($valueStringNotEmpty, $stringStarts)) {
            return static::message();
        }

        return null;
    }
}
