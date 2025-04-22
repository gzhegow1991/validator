<?php

namespace Gzhegow\Validator\Translator;

use Gzhegow\Validator\Rule\RuleInterface;
use Gzhegow\Validator\Exception\RuntimeException;


class ValidatorPassTranslator implements ValidatorTranslatorInterface
{
    public function translate(
        ?string $message, ?\Throwable $throwable,
        array $theValue, $theKey, array $thePath,
        RuleInterface $rule, array $ruleParameters
    ) : string
    {
        if (null !== $throwable) {
            throw new RuntimeException(
                [ 'Unable to ' . __METHOD__ ], $throwable
            );
        }

        return (is_string($message) && ('' !== $message))
            ? $message
            : 'validation.fatal';
    }
}
