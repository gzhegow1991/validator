<?php

namespace Gzhegow\Validator;

use Gzhegow\Validator\Core\Rule\RuleInterface;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\Core\Validation\Validation;
use Gzhegow\Validator\Core\Replacer\RuleReplacer;
use Gzhegow\Validator\Core\Replacer\ReplacerInterface;
use Illuminate\Contracts\Validation\Factory as IlluminateValidatorFactoryContract;


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
     * @var IlluminateValidatorFactoryContract
     */
    protected $illuminateValidatorFactory;


    public function __construct(
        ValidatorFactoryInterface $factory,
        ValidatorProcessorInterface $processor,
        //
        IlluminateValidatorFactoryContract $illuminateValidatorFactory
    )
    {
        $this->factory = $factory;
        $this->processor = $processor;

        $this->illuminateValidatorFactory = $illuminateValidatorFactory;
    }


    public function builder() : Validation
    {
        $validation = $this->factory->newValidation(
            $this->illuminateValidatorFactory
        );

        return $validation;
    }

    /**
     * @param array<string, callable|callable[]> $filters
     */
    public function make(
        array $data,
        //
        array $rules = [],
        array $filters = [],
        array $messages = [],
        array $attributes = []
    ) : Validation
    {
        $validation = $this->factory->newValidation(
            $this->illuminateValidatorFactory
        );

        $validation->addData($data);

        $validation->addRulesMap(
            $rules,
            $messages,
            $attributes,
            $filters
        );

        return $validation;
    }


    /**
     * @param class-string<RuleInterface> $ruleClass
     *
     * @return static
     */
    public function extend(
        string $name, string $ruleClass, string $replacerClass = null,
        bool $isImplicit = null, bool $isDependent = null
    )
    {
        // > implicit-правила это правила которые срабатывают даже если значение пустое
        // > например, проверка обязательности или наличие ключа в массиве
        $isImplicit = $isImplicit ?? false;

        // > dependent-правила это правила которые срабатывают только если есть или нет другого поля
        // > например, например bool_if:another_field
        $isDependent = $isDependent ?? false;

        $isRuleClass = is_a($ruleClass, RuleInterface::class, true);
        if (! $isRuleClass) {
            throw new LogicException(
                [ 'The `ruleClass` should be class-string of: ' . RuleInterface::class, $ruleClass ]
            );
        }

        if (null !== $replacerClass) {
            $isReplacerClass = is_a($replacerClass, ReplacerInterface::class, true);
            if (! $isReplacerClass) {
                throw new LogicException(
                    [ 'The `replacerClass` should be class-string of: ' . ReplacerInterface::class, $ruleClass ]
                );
            }

        } else {
            $replacerClass = RuleReplacer::class;
        }

        $this->illuminateValidatorFactory->extend(
            $name, $ruleClass
        );

        if ($isImplicit) {
            $this->illuminateValidatorFactory->extendImplicit(
                $name, $ruleClass
            );
        }

        if ($isDependent) {
            $this->illuminateValidatorFactory->extendDependent(
                $name, $ruleClass
            );
        }

        $this->illuminateValidatorFactory->replacer($name, $replacerClass);

        return $this;
    }

    /**
     * @param class-string<RuleInterface> $ruleClass
     *
     * @return static
     */
    public function extendImplicit(
        string $name, string $ruleClass, string $replacerClass = null,
        bool $isDependent = null
    )
    {
        $this->extend(
            $name, $ruleClass, $replacerClass,
            true, $isDependent
        );

        return $this;
    }

    /**
     * @param class-string<RuleInterface> $ruleClass
     *
     * @return static
     */
    public function extendDependent(
        string $name, string $ruleClass, string $replacerClass = null,
        bool $isImplicit = null
    )
    {
        $this->extend(
            $name, $ruleClass, $replacerClass,
            $isImplicit, true
        );

        return $this;
    }
}
