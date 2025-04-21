<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Str;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
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

        if (! Lib::type()->string($stringToContains, $parameter0)) {
            return 'validation.fatal';
        }

        if ('' === $stringToContains) {
            return null;
        }

        $fnStrpos = Lib::str()->mb_func('strpos');

        if (false === $fnStrpos($string, $stringToContains)) {
            return static::message();
        }

        return null;
    }
}
