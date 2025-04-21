<?php

namespace Gzhegow\Validator;

use Gzhegow\Validator\Rule\GenericRule;
use Gzhegow\Validator\Rule\RuleInterface;
use Gzhegow\Validator\Validation\ValidationInterface;
use Gzhegow\Validator\RuleRegistry\RuleRegistryInterface;
use Gzhegow\Validator\Processor\ValidatorProcessorInterface;
use Gzhegow\Validator\Translator\ValidatorTranslatorInterface;


interface ValidatorFactoryInterface
{
    public function newValidation(
        ValidatorProcessorInterface $processor,
        ValidatorTranslatorInterface $translator,
        //
        RuleRegistryInterface $registry
    ) : ValidationInterface;


    public function newRule(
        GenericRule $generic
    ) : RuleInterface;


    public function newFilterObject(
        string $filterClass, ...$args
    ) : object;
}
