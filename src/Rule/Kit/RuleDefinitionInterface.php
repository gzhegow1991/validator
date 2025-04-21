<?php

namespace Gzhegow\Validator\Rule\Kit;

use Gzhegow\Validator\Rule\RuleInterface;


interface RuleDefinitionInterface
{
    /**
     * @return array<class-string<RuleInterface>, bool|RuleInterface>
     */
    public static function rules() : array;
}
