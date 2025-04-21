<?php

namespace Gzhegow\Validator\Rule\Kit\Implicit;


class PresentSetRule extends PresentedWithAllRule
{
    const NAME = 'present_set';

    public static function message(array $conditions = []) : string
    {
        return 'validation.present_set';
    }
}
