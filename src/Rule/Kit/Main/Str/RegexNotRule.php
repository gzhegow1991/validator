<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Str;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class RegexNotRule extends AbstractRule
{
    const NAME = 'regex_not';

    public static function message(array $conditions = []) : string
    {
        return 'validation.regex_not';
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
        $parameter1 = $this->parameters[ 1 ] ?? null;

        if (! Lib::type()->string($regex, $parameter0)) {
            return 'validation.fatal';
        }

        $regexFlags = '';
        if (null !== $parameter1) {
            if (! Lib::type()->string_not_empty($regexFlags, $parameter1)) {
                return 'validation.fatal';
            }
        }

        $regexp = "/{$regex}/{$regexFlags}";

        $isValid = false;
        try {
            $isValid = preg_match($regexp, '');
        }
        catch ( \Throwable $e ) {
        }

        if (false === $isValid) {
            return 'validation.fatal';
        }

        if (0 !== preg_match($regexp, $string)) {
            return static::message();
        }

        return null;
    }
}
