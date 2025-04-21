<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Cmp\Size;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class SizeMaxRule extends AbstractRule
{
    const NAME = 'size_max';

    public static function message(array $conditions = []) : string
    {
        return 'validation.size_max';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        if (! isset($this->parameters[ 0 ])) {
            return 'validation.fatal';
        }

        $parameter0 = $this->parameters[ 0 ];
        $parameter1 = $this->parameters[ 1 ] ?? null;

        $theType = Lib::type();

        if (! $theType->int($sizeModel, $parameter0)) {
            return 'validation.fatal';
        }

        $mode = 'size';
        if (null !== $parameter1) {
            if (! $theType->string_not_empty($mode, $parameter1)) {
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

        if ($size > $sizeModel) {
            return static::message();
        }

        return null;
    }
}
