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

        $theType = Lib::type();

        if (! $theType->string_not_empty($value[ 0 ])->isOk([ &$string ])) {
            return static::message();
        }

        if (! $theType->string($parameter0)->isOk([ &$needle ])) {
            throw new LogicException(
                [ 'The `parameters[0]` should be string', $parameter0 ]
            );
        }

        if ('' === $needle) {
            return null;
        }

        $theStr = Lib::str();

        $fnStrlen = $theStr->mb_func('strlen');

        $len = $fnStrlen($string);
        $lenContains = $fnStrlen($needle);

        $fnStrpos = $theStr->mb_func('strpos');

        $pos = $fnStrpos($string, $needle);

        if (false === $pos) {
            return static::message();
        }

        if ($pos !== ($len - $lenContains)) {
            return static::message();
        }

        return null;
    }
}
