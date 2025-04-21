<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Str;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
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

        if (! Lib::type()->string($string, $value[ 0 ])) {
            return static::message();
        }

        if ('' === $string) {
            return static::message();
        }

        if (! isset($this->parameters[ 0 ])) {
            return 'validation.fatal';
        }

        $parameter0 = $this->parameters[ 0 ];

        if (! Lib::type()->string($stringToEnds, $parameter0)) {
            return 'validation.fatal';
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
