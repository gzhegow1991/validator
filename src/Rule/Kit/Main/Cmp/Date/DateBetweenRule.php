<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Cmp\Date;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Validator\Exception\LogicException;
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
            throw new LogicException(
                'The `parameters[0]` should be present, and known as `dateMin`'
            );
        }

        if (! isset($this->parameters[ 1 ])) {
            throw new LogicException(
                'The `parameters[1]` should be present, and known as `dateMax`'
            );
        }

        $parameter0 = $this->parameters[ 0 ];
        $parameter1 = $this->parameters[ 1 ];
        $parameter2 = $this->parameters[ 2 ] ?? null;

        $theType = Lib::type();

        if (! $theType->date($value[ 0 ])->isOk([ &$date ])) {
            return static::message();
        }

        if (! $theType->date($parameter0)->isOk([ &$dateMin ])) {
            throw new LogicException(
                [ 'The `parameters[0]` should be valid date', $parameter0 ]
            );
        }

        if (! $theType->date($parameter1)->isOk([ &$dateMax ])) {
            throw new LogicException(
                [ 'The `parameters[1]` should be valid date', $parameter1 ]
            );
        }

        if ($dateMin > $dateMax) {
            throw new LogicException(
                [ 'The `dateMin` should be greater than `dateMax`', $dateMin, $dateMax ]
            );
        }

        $flagsMode = _CMP_MODE_DATE_VS_USEC;
        if (null !== $parameter2) {
            if (! $theType->string_not_empty($parameter2)->isOk([ &$mode ])) {
                throw new LogicException(
                    [ 'The `parameters[2]` should be non-empty string, and known as `mode`', $parameter2 ]
                );
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
