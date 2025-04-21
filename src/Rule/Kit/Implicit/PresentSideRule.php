<?php

namespace Gzhegow\Validator\Rule\Kit\Implicit;


class PresentSideRule extends PresentedWithoutOneRule
{
    const NAME = 'present_side';

    public static function message(array $conditions = []) : string
    {
        return 'validation.present_side';
    }
}
