<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\Field;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\Validation\ValidationInterface;


class NeqFieldRule extends AbstractRule
{
    const NAME = 'neq_field';

    public static function message(array $conditions = []) : string
    {
        return 'validation.neq_field';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        if (! isset($this->parameters[ 0 ])) {
            throw new LogicException(
                'The `parameters[0]` should be present, and known as `dateNeqField`'
            );
        }

        $parameter0 = $this->parameters[ 0 ];
        $parameter1 = $this->parameters[ 1 ] ?? null;

        $fieldPath = $validation->fieldPathOrAbsolute($parameter0, $path);
        $fieldValue = $validation->get($fieldPath, [ $this ]);
        if ($this === $fieldValue) {
            return static::message();
        }

        $flagsMode = null;
        if (null !== $parameter1) {
            if (! Lib::type()->int($flagsMode, $parameter1)) {
                throw new LogicException(
                    'The `parameters[1]` should be integer, and known as `flags`'
                );
            }
        }

        $fnCmp = Lib::cmp()->fnCompareValues(
            $flagsMode,
            _CMP_RESULT_NAN_RETURN
        );

        $status = $fnCmp($value[ 0 ], $fieldValue);

        if (! is_int($status)) {
            return null;
        }

        if (0 === $status) {
            return static::message();
        }

        return null;
    }
}
