<?php

namespace Gzhegow\Validator\Package\Illuminate\Validation;

use Gzhegow\Validator\Core\Rule\RuleInterface;
use Gzhegow\Validator\Core\Replacer\ReplacerInterface;
use Gzhegow\Validator\Exception\Runtime\ValidationException;
use Gzhegow\Validator\Exception\Runtime\InspectionException;
use Illuminate\Contracts\Validation\Validator as IlluminateValidatorContract;


interface ValidatorInterface extends
    IlluminateValidatorContract
{
    /**
     * @return static
     */
    public function setLocale(?string $locale);


    /**
     * @return static
     *
     * @throws ValidationException
     */
    public function validate() : Validator;

    /**
     * @return static
     *
     * @throws InspectionException
     */
    public function inspect() : Validator;


    public function all() : array;

    /**
     * @return array<int|string, bool>
     */
    public function allAttributes() : array;


    public function validated() : array;

    public function validatedAttributes() : array;


    public function valid() : array;

    public function validAttributes() : array;


    public function invalid() : array;

    public function invalidAttributes() : array;


    public function hasRuleInstance(string $key) : ?RuleInterface;

    public function getRuleInstance(string $key) : RuleInterface;

    /**
     * @return static
     */
    public function registerRuleInstance(string $key, RuleInterface $rule);


    public function hasReplacerInstance(string $key) : ?ReplacerInterface;

    public function getReplacerInstance(string $key) : ReplacerInterface;

    /**
     * @return static
     */
    public function registerReplacerInstance(string $key, ReplacerInterface $replacer);
}
