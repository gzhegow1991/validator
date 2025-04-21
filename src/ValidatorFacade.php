<?php

namespace Gzhegow\Validator;

use Gzhegow\Validator\Validation\Validation;
use Gzhegow\Validator\RuleRegistry\RuleRegistryInterface;
use Gzhegow\Validator\Processor\ValidatorProcessorInterface;
use Gzhegow\Validator\Translator\ValidatorTranslatorInterface;


class ValidatorFacade implements ValidatorInterface
{
    /**
     * @var ValidatorFactoryInterface
     */
    protected $factory;
    /**
     * @var ValidatorProcessorInterface
     */
    protected $processor;
    /**
     * @var ValidatorTranslatorInterface
     */
    protected $translator;

    /**
     * @var RuleRegistryInterface
     */
    protected $registry;


    public function __construct(
        ValidatorFactoryInterface $factory,
        ValidatorProcessorInterface $processor,
        ValidatorTranslatorInterface $translator,
        //
        RuleRegistryInterface $registry
    )
    {
        $this->factory = $factory;
        $this->processor = $processor;
        $this->translator = $translator;

        $this->registry = $registry;
    }


    public function new() : Validation
    {
        $validation = $this->factory->newValidation(
            $this->processor,
            $this->translator,
            //
            $this->registry
        );

        return $validation;
    }
}
