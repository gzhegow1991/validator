<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Str;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\Validation\ValidationInterface;


class RegexRule extends AbstractRule
{
    const NAME = 'regex';

    public static function message(array $conditions = []) : string
    {
        return 'validation.regex';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        if (! isset($this->parameters[ 0 ])) {
            throw new LogicException(
                'The `parameters[0]` should be present, and known as `regex`'
            );
        }

        $parameter0 = $this->parameters[ 0 ];
        $parameter1 = $this->parameters[ 1 ] ?? null;

        if (! Lib::type()->string_not_empty($string, $value[ 0 ])) {
            return static::message();
        }

        if (! Lib::type()->string_not_empty($regex, $parameter0)) {
            throw new LogicException(
                [ 'The `parameters[0]` should be non-empty string', $parameter0 ]
            );
        }

        $regexFlags = '';
        if (null !== $parameter1) {
            if (! Lib::type()->string_not_empty($regexFlags, $parameter1)) {
                throw new LogicException(
                    [ 'The `parameters[1]` should be non-empty string, and known as `flags`', $parameter1 ]
                );
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
            throw new LogicException(
                [ 'The `regexp` should be valid regular expression', $regexp ]
            );
        }

        if (0 === preg_match($regexp, $string)) {
            return static::message();
        }

        return null;
    }
}
