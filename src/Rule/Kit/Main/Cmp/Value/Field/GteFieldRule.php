<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Cmp\Value\Field;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class GteFieldRule extends AbstractRule
{
    const NAME = 'gte_field';

    public static function message(array $conditions = []) : string
    {
        return 'validation.gte_field';
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

        $fieldPath = $validation->fieldPathOrAbsolute($parameter0, $path);
        $fieldValue = $validation->get($fieldPath, [ $this ]);
        if ($this === $fieldValue) {
            return static::message();
        }

        $flagsMode = null;
        if (null !== $parameter1) {
            if (! Lib::type()->int($flagsMode, $parameter1)) {
                return 'validation.fatal';
            }
        }

        $fnCmp = Lib::cmp()->fnCompareValues(
            $flagsMode,
            _CMP_RESULT_NAN_RETURN
        );

        $status = $fnCmp($value[ 0 ], $fieldValue);

        if (! is_int($status)) {
            return static::message();
        }

        if (0 > $status) {
            return static::message();
        }

        return null;
    }
}
