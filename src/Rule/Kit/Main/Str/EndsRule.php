<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Str;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\Validation\ValidationInterface;


class EndsRule extends AbstractRule
{
    const NAME = 'ends';

    public static function message(array $conditions = []) : string
    {
        return 'validation.ends';
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

        if (! Lib::type()->string($stringToEnds, $parameter0)) {
            throw new LogicException(
                [ 'The `parameters[0]` should be string', $parameter0 ]
            );
        }

        if ('' === $stringToEnds) {
            return null;
        }

        $fnStrlen = Lib::str()->mb_func('strlen');

        $len = $fnStrlen($string);
        $lenContains = $fnStrlen($stringToEnds);

        $fnStrpos = Lib::str()->mb_func('strpos');

        $pos = $fnStrpos($string, $stringToEnds);

        if (false === $pos) {
            return static::message();
        }

        if ($pos !== ($len - $lenContains)) {
            return static::message();
        }

        return null;
    }
}
