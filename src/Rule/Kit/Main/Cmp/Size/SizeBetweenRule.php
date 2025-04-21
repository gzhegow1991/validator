<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Cmp\Size;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class SizeBetweenRule extends AbstractRule
{
    const NAME = 'size_between';

    public static function message(array $conditions = []) : string
    {
        return 'validation.size_between';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $theType = Lib::type();

        if (! isset($this->parameters[ 0 ])) {
            return 'validation.fatal';
        }

        if (! isset($this->parameters[ 1 ])) {
            return 'validation.fatal';
        }

        $parameter0 = $this->parameters[ 0 ];
        $parameter1 = $this->parameters[ 1 ];
        $parameter2 = $this->parameters[ 2 ] ?? null;

        if (! $theType->int($sizeMin, $parameter0)) {
            return 'validation.fatal';
        }

        if (! $theType->int($sizeMax, $parameter1)) {
            return 'validation.fatal';
        }

        if ($sizeMin > $sizeMax) {
            return 'validation.fatal';
        }

        $mode = 'size';
        if (null !== $parameter2) {
            if (! $theType->string_not_empty($mode, $parameter2)) {
                return 'validation.fatal';
            }
        }

        if ('size' === $mode) {
            $fnSize = [ Lib::php(), 'size' ];

        } elseif ('count' === $mode) {
            $fnSize = [ Lib::php(), 'count' ];

        } elseif ('strsize' === $mode) {
            $fnSize = [ Lib::str(), 'strsize' ];

        } elseif ('strlen' === $mode) {
            $fnSize = [ Lib::str(), 'strlen' ];

        } else {
            return 'validation.fatal';
        }

        $size = $fnSize($value[ 0 ]);

        if (! is_int($size)) {
            return static::message();
        }

        if ($size < $sizeMin) {
            return static::message();
        }

        if ($size > $sizeMax) {
            return static::message();
        }

        return null;
    }
}
