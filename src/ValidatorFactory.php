<?php

namespace Gzhegow\Validator;

use Gzhegow\Validator\Rule\GenericRule;
use Gzhegow\Validator\Rule\RuleInterface;
use Gzhegow\Validator\Validation\Validation;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\RuleRegistry\RuleRegistryInterface;
use Gzhegow\Validator\Processor\ValidatorProcessorInterface;
use Gzhegow\Validator\Translator\ValidatorTranslatorInterface;


class ValidatorFactory implements ValidatorFactoryInterface
{
    public function newValidation(
        ValidatorProcessorInterface $processor,
        ValidatorTranslatorInterface $translator,
        //
        RuleRegistryInterface $registry
    ) : ValidationInterface
    {
        return new Validation(
            $this,
            $processor,
            $translator,
            //
            $registry
        );
    }


    public function newRule(
        GenericRule $generic
    ) : RuleInterface
    {
        $ruleClass = $generic->getRuleClass();
        $ruleParameters = $generic->getRuleClassParameters();

        return new $ruleClass($ruleParameters);
    }


    public function newFilterObject(
        string $filterClass, ...$args
    ) : object
    {
        return new $filterClass(...$args);
    }
}
