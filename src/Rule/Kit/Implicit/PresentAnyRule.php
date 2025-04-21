<?php

namespace Gzhegow\Validator\Rule\Kit\Implicit;


class PresentAnyRule extends PresentedWithoutAllRule
{
    const NAME = 'present_any';

    public static function message(array $conditions = []) : string
    {
        return 'validation.present_any';
    }
}
