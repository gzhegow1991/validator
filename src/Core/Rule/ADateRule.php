<?php

namespace Gzhegow\Validator\Core\Rule;

use Gzhegow\Calendar\Calendar;
use Gzhegow\Validator\Package\Illuminate\Validation\ValidatorInterface;


class ADateRule extends AbstractRule
{
    public function validate(
        $attribute, $value, $parameters,
        ValidatorInterface $validator
    ) : bool
    {
        $formatsArray = $parameters[ 0 ] ?? null;
        $formatsArray = is_array($formatsArray)
            ? $formatsArray
            : (is_string($formatsArray) ? [ $formatsArray ] : null);

        $formats = null;
        if (null !== $formatsArray) {
            $formats = [];

            foreach ( $formatsArray as $format ) {
                $list = explode(';', $format);
                $list = array_map('trim', $list);

                $formats = array_merge($formats, $list);
            }
        }

        if (class_exists('\Gzhegow\Calendar\Calendar')) {
            return (null !== Calendar::parseDateTime($value, $formats));

        } else {
            if ($value instanceof \DateTimeInterface) {
                return true;

            } else {
                $dt = null;

                if (null === $formats) {
                    try {
                        $dt = new \DateTime($value);
                    }
                    catch ( \Throwable $e ) {
                    }

                } else {
                    foreach ( $formats as $format ) {
                        try {
                            $dt = \DateTime::createFromFormat($format, $value);
                        }
                        catch ( \Throwable $e ) {
                        }

                        if ($dt) {
                            break;
                        }
                    }
                }

                if ($dt) {
                    return true;
                }
            }
        }

        return false;
    }
}
