<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Cmp\Date;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Validation\ValidationInterface;


class DateBetweenRule extends AbstractRule
{
    const NAME = 'date_between';

    public static function message(array $conditions = []) : string
    {
        return 'validation.date_between';
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

        if (! isset($this->parameters[ 1 ])) {
            return 'validation.fatal';
        }

        $parameter0 = $this->parameters[ 0 ];
        $parameter1 = $this->parameters[ 1 ];
        $parameter2 = $this->parameters[ 2 ] ?? null;

        $theType = Lib::type();

        if (! $theType->date($date, $value[ 0 ])) {
            return static::message();
        }

        if (! $theType->date($dateMin, $parameter0)) {
            return 'validation.fatal';
        }

        if (null === $parameter1) {
            $dateMax = $dateMin;

        } elseif (! $theType->date($dateMax, $parameter1)) {
            return 'validation.fatal';
        }

        if ($dateMin > $dateMax) {
            return 'validation.fatal';
        }

        $flagsMode = _CMP_MODE_DATE_VS_USEC;
        if (null !== $parameter2) {
            if (! $theType->string_not_empty($mode, $parameter2)) {
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

        $statusMin = $fnCmp($date, $dateMin);

        if (0 > $statusMin) {
            return static::message();
        }

        $statusMax = $fnCmp($date, $dateMax);

        if (0 < $statusMax) {
            return static::message();
        }

        return null;
    }
}
