<?php

namespace Gzhegow\Validator\Rule;

use Gzhegow\Validator\Validation\ValidationInterface;


interface RuleInterface
{
    public static function parse(
        string $ruleName,
        array $ruleArguments = []
    ) : GenericRule;


    const NAME = '';

    public static function message(array $conditions = []) : string;


    public function validate(
        array $value, string $key, array $path,
        ValidationInterface $validation
    ) : ?string;


    public function getParameters() : array;
}
