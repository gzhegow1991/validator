<?php

namespace Gzhegow\Validator;

use Gzhegow\Validator\Core\Rule\RuleInterface;
use Gzhegow\Validator\Core\Validation\Validation;
use Gzhegow\Validator\Core\Replacer\ReplacerInterface;
use Illuminate\Container\Container as IlluminateContainer;
use Gzhegow\Validator\Core\Validation\ValidationInterface;
use Illuminate\Filesystem\Filesystem as IlluminateFilesystem;
use Illuminate\Translation\Translator as IlluminateTranslator;
use Illuminate\Validation\Factory as IlluminateValidatorFactory;
use Illuminate\Translation\FileLoader as IlluminateTranslationLoader;
use Illuminate\Contracts\Container\Container as IlluminateContainerContract;
use Illuminate\Contracts\Translation\Translator as IlluminateTranslatorContract;
use Illuminate\Contracts\Validation\Factory as IlluminateValidatorFactoryContract;
use Illuminate\Contracts\Translation\Loader as IlluminateTranslationLoaderContract;
use Gzhegow\Validator\Package\Illuminate\Validation\Validator as IlluminateValidator;


class ValidatorFactory implements ValidatorFactoryInterface
{
    public function newValidation(
        IlluminateValidatorFactoryContract $illuminateFactory
    ) : ValidationInterface
    {
        return new Validation(
            $this,
            //
            $illuminateFactory
        );
    }


    /**
     * @param class-string<RuleInterface> $ruleClass
     * @param                             ...$args
     *
     * @return RuleInterface
     */
    public function newRule(string $ruleClass, ...$args) : RuleInterface
    {
        return new $ruleClass(...$args);
    }

    /**
     * @param class-string<ReplacerInterface> $replacerClass
     * @param                                 ...$args
     */
    public function newReplacer(string $replacerClass, ...$args) : ReplacerInterface
    {
        return new $replacerClass(...$args);
    }


    public function newIlluminateContainer() : IlluminateContainerContract
    {
        return new IlluminateContainer();
    }

    public function newIlluminateTranslationLoader() : IlluminateTranslationLoaderContract
    {
        $translationsDir = __DIR__ . '/../storage/package/illuminate/validation/translations';

        $filesystem = new IlluminateFilesystem();

        $fileLoader = new IlluminateTranslationLoader(
            $filesystem,
            $translationsDir
        );

        return $fileLoader;
    }

    public function newIlluminateTranslator(
        IlluminateTranslationLoaderContract $loader = null,
        //
        string $localeDefault = null
    ) : IlluminateTranslatorContract
    {
        $loader = $loader ?? $this->newIlluminateTranslationLoader();

        $localeDefault = $localeDefault ?? 'en';

        $translator = new IlluminateTranslator(
            $loader,
            //
            $localeDefault
        );

        return $translator;
    }

    public function newIlluminateValidatorFactory(
        ValidatorProcessorInterface $processor,
        //
        IlluminateContainerContract $container = null,
        IlluminateTranslatorContract $translator = null
    ) : IlluminateValidatorFactoryContract
    {
        $container = $container ?? $this->newIlluminateContainer();
        $translator = $translator ?? $this->newIlluminateTranslator();

        $illuminateFactory = new IlluminateValidatorFactory(
            $translator,
            $container
        );

        $illuminateFactory->resolver(function (
            $translator,
            //
            $data,
            $rules,
            $messages,
            $customAttributes
        ) use (
            $processor
        ) {
            $validator = new IlluminateValidator(
                $this,
                $processor,
                //
                $translator,
                //
                $data,
                $rules,
                $messages,
                $customAttributes
            );

            return $validator;
        });

        return $illuminateFactory;
    }
}
