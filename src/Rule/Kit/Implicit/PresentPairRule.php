<?php

namespace Gzhegow\Validator\Rule\Kit\Implicit;


class PresentPairRule extends PresentedWithOneRule
{
    const NAME = 'present_pair';

    public static function message(array $conditions = []) : string
    {
        return 'validation.present_pair';
    }
}
