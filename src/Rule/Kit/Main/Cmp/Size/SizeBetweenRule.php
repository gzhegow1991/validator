<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Cmp\Size;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
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
            throw new LogicException(
                'The `parameters[0]` should be present, and known as `sizeMin`'
            );
        }

        if (! isset($this->parameters[ 1 ])) {
            throw new LogicException(
                'The `parameters[0]` should be present, and known as `sizeMax`'
            );
        }

        $parameter0 = $this->parameters[ 0 ];
        $parameter1 = $this->parameters[ 1 ];
        $parameter2 = $this->parameters[ 2 ] ?? null;

        if (! $theType->int($sizeMin, $parameter0)) {
            throw new LogicException(
                [ 'The `parameters[0]` should be integer', $parameter0 ]
            );
        }

        if (! $theType->int($sizeMax, $parameter1)) {
            throw new LogicException(
                [ 'The `parameters[1]` should be integer', $parameter1 ]
            );
        }

        if ($sizeMin > $sizeMax) {
            throw new LogicException(
                [ 'The `sizeMin` should be greater than `sizeMax`', $sizeMin, $sizeMax ]
            );
        }

        $mode = 'size';
        if (null !== $parameter2) {
            if (! $theType->string_not_empty($mode, $parameter2)) {
                throw new LogicException(
                    [ 'The `parameters[2]` should be non-empty string, and known as `mode`', $parameter2 ]
                );
            }

            $modes = [
                'size'    => true,
                'count'   => true,
                'strsize' => true,
                'strlen'  => true,
            ];

            if (! isset($modes[ $mode ])) {
                throw new LogicException(
                    [
                        ''
                        . 'The `mode` should be one of: '
                        . '[ ' . implode(' ][ ', array_keys($modes)) . ' ]',
                        //
                        $mode,
                    ]
                );
            }
        }

        $fnSize = '';
        if ('size' === $mode) {
            $fnSize = [ Lib::php(), 'size' ];

        } elseif ('count' === $mode) {
            $fnSize = [ Lib::php(), 'count' ];

        } elseif ('strsize' === $mode) {
            $fnSize = [ Lib::str(), 'strsize' ];

        } elseif ('strlen' === $mode) {
            $fnSize = [ Lib::str(), 'strlen' ];
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
