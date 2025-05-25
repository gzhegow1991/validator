<?php

namespace Gzhegow\Validator\Rule\Kit\Main\Format;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Rule\AbstractRule;
use Gzhegow\Lib\Modules\Php\Result\Result;
use Gzhegow\Validator\Validation\ValidationInterface;


class JsonRule extends AbstractRule
{
    const NAME = 'json';

    public static function message(array $conditions = []) : string
    {
        return 'validation.json';
    }


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string
    {
        if ([] === $value) return static::message();

        $jsonDecoded = Lib::format()->json()->json_decode(
            $value[ 0 ], null,
            null, null,
            Result::asValue([ $this ])
        );

        $status = ($jsonDecoded !== $this);

        if (! $status) {
            return static::message();
        }

        return null;
    }
}
