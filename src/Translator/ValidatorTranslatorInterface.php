<?php

namespace Gzhegow\Validator\Translator;

use Gzhegow\Validator\Rule\RuleInterface;


interface ValidatorTranslatorInterface
{
    public function translate(
        ?string $message, ?\Throwable $throwable,
        array $theValue, $theKey, array $thePath,
        RuleInterface $rule, array $ruleParameters
    ) : string;
}
