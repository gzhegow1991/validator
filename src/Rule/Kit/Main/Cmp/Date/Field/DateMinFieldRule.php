<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Cmp\Date\Field;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class DateMinFieldRule extends AbstractRule
{
    const NAME = 'date_min_field';

    public static function message(array $conditions = []) : string
    {
        return 'validation.date_min_field';
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

        if (! $theType->date($date, $value[ 0 ])) {
            return static::message();
        }

        $fieldPath = $validation->fieldPathOrAbsolute($parameter0, $path);
        $fieldValue = $validation->get($fieldPath, [ $this ]);
        if ($this === $fieldValue) {
            return static::message();
        }

        if (! $theType->date($dateModel, $fieldValue)) {
            return 'validation.fatal';
        }

        $flagsMode = _CMP_MODE_DATE_VS_USEC;
        if (null !== $parameter1) {
            if (! $theType->string_not_empty($mode, $parameter1)) {
                return 'validation.fatal';
            }

            $modes = [
                'year'  => _CMP_MODE_DATE_VS_YEAR,
                'month' => _CMP_MODE_DATE_VS_MONTH,
                'day'   => _CMP_MODE_DATE_VS_DAY,
                'hour'  => _CMP_MODE_DATE_VS_HOUR,
                'min'   => _CMP_MODE_DATE_VS_MIN,
                'sec'   => _CMP_MODE_DATE_VS_SEC,
                'msec'  => _CMP_MODE_DATE_VS_MSEC,
                'usec'  => _CMP_MODE_DATE_VS_USEC,
            ];

            $flagsMode = $modes[ $mode ] ?? null;

            if (null === $flagsMode) {
                return 'validation.fatal';
            }
        }

        $fnCmp = Lib::cmp()->fnCompareDates(
            $flagsMode,
            _CMP_RESULT_NAN_RETURN
        );

        $status = $fnCmp($date, $dateModel);

        if (0 > $status) {
            return static::message();
        }

        return null;
    }
}
