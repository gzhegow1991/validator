<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Cmp\Size;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
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
            throw new LogicException(
                'The `parameters[0]` should be present, and known as `sizeMax`'
            );
        }

        $parameter0 = $this->parameters[ 0 ];
        $parameter1 = $this->parameters[ 1 ] ?? null;

        $theType = Lib::type();

        if (! $theType->int($sizeMax, $parameter0)) {
            throw new LogicException(
                [ 'The `parameters[0]` should be integer', $parameter0 ]
            );
        }

        $mode = 'size';
        if (null !== $parameter1) {
            if (! $theType->string_not_empty($mode, $parameter1)) {
                throw new LogicException(
                    [ 'The `parameters[2]` should be non-empty string, and known as `mode`', $parameter1 ]
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

        if ($size > $sizeMax) {
            return static::message();
        }

        return null;
    }
}
