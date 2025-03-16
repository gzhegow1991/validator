<?php

namespace Gzhegow\Validator;

use Gzhegow\Validator\Core\Rule\RuleInterface;
use Gzhegow\Validator\Core\Replacer\ReplacerInterface;
use Gzhegow\Validator\Core\Validation\ValidationInterface;
use Illuminate\Contracts\Container\Container as IlluminateContainerContract;
use Illuminate\Contracts\Translation\Translator as IlluminateTranslatorContract;
use Illuminate\Contracts\Validation\Factory as IlluminateValidatorFactoryContract;
use Illuminate\Contracts\Translation\Loader as IlluminateTranslationLoaderContract;


interface ValidatorFactoryInterface
{
    public function newValidation(
        IlluminateValidatorFactoryContract $illuminateFactory
    ) : ValidationInterface;


    /**
     * @param class-string<RuleInterface> $ruleClass
     * @param                             ...$args
     */
    public function newRule(string $ruleClass, ...$args) : RuleInterface;

    /**
     * @param class-string<ReplacerInterface> $replacerClass
     * @param                                 ...$args
     */
    public function newReplacer(string $replacerClass, ...$args) : ReplacerInterface;


    public function newIlluminateContainer() : IlluminateContainerContract;

    public function newIlluminateTranslationLoader() : IlluminateTranslationLoaderContract;

    public function newIlluminateTranslator(
        IlluminateTranslationLoaderContract $loader = null,
        //
        string $localeDefault = null
    ) : IlluminateTranslatorContract;

    public function newIlluminateValidatorFactory(
        ValidatorProcessorInterface $processor,
        //
        IlluminateContainerContract $container = null,
        IlluminateTranslatorContract $translator = null
    ) : IlluminateValidatorFactoryContract;
}
