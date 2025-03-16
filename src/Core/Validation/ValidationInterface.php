<?php

namespace Gzhegow\Validator\Core\Validation;

use Gzhegow\Validator\Core\Rule\RuleInterface;
use Gzhegow\Validator\Exception\Runtime\ValidationException;
use Gzhegow\Validator\Exception\Runtime\InspectionException;
use Gzhegow\Validator\Package\Illuminate\Validation\ValidatorInterface;
use Illuminate\Contracts\Support\MessageBag as IlluminateMessageBagContract;


interface ValidationInterface
{
    public function getIlluminateValidator() : ValidatorInterface;


    /**
     * @return static
     */
    public function merge(ValidationInterface $validation);


    /**
     * @return static
     *
     * @throws ValidationException
     */
    public function validate(&$bind = null);

    /**
     * @return static
     *
     * @throws InspectionException
     */
    public function inspect(&$bind = null);


    public function validated(&$bind = null) : array;

    public function validatedAttributes() : array;


    public function valid(&$bind = null) : array;

    public function validAttributes() : array;


    public function invalid(&$bind = null) : array;

    public function invalidAttributes() : array;


    public function messageBag() : IlluminateMessageBagContract;

    public function messages() : array;


    /**
     * @return static
     */
    public function modeApi(bool $modeApi = null);

    /**
     * @return static
     */
    public function modeWeb(bool $modeWeb = null);


    /**
     * @return static
     */
    public function addData(array $data);


    /**
     * @param array<string|RuleInterface>|string|RuleInterface $rules
     * @param callable                                         ...$filters
     *
     * @return static
     */
    public function addRules(string $key, $rules, ...$filters);

    /**
     * @param array<string, array<string|RuleInterface>|string|RuleInterface> $rules
     * @param array<string, callable[]>                                       $filters
     * @param array<string, string>                                           $messages
     * @param array<string, string>                                           $attributes
     *
     * @return static
     */
    public function addRulesMap(
        array $rules = [],
        array $filters = [],
        array $messages = [],
        array $attributes = []
    );


    /**
     * @return static
     */
    public function setLocale(?string $locale);
}
