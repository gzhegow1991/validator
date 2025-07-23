<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Str;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
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

        if (! isset($this->parameters[ 0 ])) {
            throw new LogicException(
                'The `parameters[0]` should be present, and known as `regex`'
            );
        }

        $parameter0 = $this->parameters[ 0 ];
        $parameter1 = $this->parameters[ 1 ] ?? null;

        $theType = Lib::type();

        if (! $theType->string_not_empty($value[ 0 ])->isOk([ &$valueStringNotEmpty ])) {
            return static::message();
        }

        if (! $theType->string_not_empty($parameter0)->isOk([ &$regex ])) {
            throw new LogicException(
                [ 'The `parameters[0]` should be non-empty string', $parameter0 ]
            );
        }

        $regexFlags = '';
        if (null !== $parameter1) {
            if (! $theType->string_not_empty($parameter1)->isOk([ &$regexFlags ])) {
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

        if (0 !== preg_match($regexp, $valueStringNotEmpty)) {
            return static::message();
        }

        return null;
    }
}
