<?php

namespace Gzhegow\Validator\Package\Illuminate\Validation;

use Gzhegow\Lib\Lib;
use Gzhegow\Validator\Core\Rule\RuleInterface;
use Symfony\Component\HttpFoundation\File\File;
use Gzhegow\Validator\ValidatorFactoryInterface;
use Gzhegow\Validator\Exception\RuntimeException;
use Illuminate\Support\Str as IlluminateSupportStr;
use Illuminate\Support\Arr as IlluminateSupportArr;
use Gzhegow\Validator\ValidatorProcessorInterface;
use Gzhegow\Validator\Core\Replacer\ReplacerInterface;
use Illuminate\Validation\Validator as IlluminateValidator;
use Gzhegow\Validator\Exception\Runtime\ValidationException;
use Gzhegow\Validator\Exception\Runtime\InspectionException;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Illuminate\Contracts\Translation\Translator as IlluminateTranslatorContract;


class Validator extends IlluminateValidator implements
    ValidatorInterface
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
     * @var string
     */
    protected $locale;

    /**
     * @var array<string, RuleInterface>
     */
    protected $registryOfRules = [];
    /**
     * @var array<string, ReplacerInterface>
     */
    protected $registryOfReplacers = [];


    public function __construct(
        ValidatorFactoryInterface $factory,
        ValidatorProcessorInterface $processor,
        //
        IlluminateTranslatorContract $translator,
        //
        array $data, array $rules, array $messages = [], array $customAttributes = []
    )
    {
        $this->factory = $factory;
        $this->processor = $processor;

        parent::__construct(
            $translator,
            //
            $data, $rules, $messages, $customAttributes
        );
    }


    /**
     * @return static
     */
    public function setLocale(?string $locale)
    {
        $this->locale = $locale;

        $translatorLocale = $locale ?? 'en';

        $this->translator->setLocale($translatorLocale);

        return $this;
    }


    /**
     * @return static
     *
     * @throws ValidationException
     */
    public function validate() : Validator
    {
        try {
            parent::validate();
        }
        catch ( IlluminateValidationException $e ) {
            throw new ValidationException(
                [ 'Ошибка валидации данных', $this ],
                $e
            );
        }

        return $this;
    }

    /**
     * @return static
     *
     * @throws InspectionException
     */
    public function inspect() : Validator
    {
        // > это исключение предполагает, что ошибки будут возвращены в виде списка, а не словаря
        // > например, если вы проверяете данные формы или входящего json - то это "валидация"

        // > а если вызываете валидатор внутри сервиса, то это "проверка инварианта"
        // > клиент будет удивлен, если он не передавал поле, а вы вернули ошибку в нем

        try {
            parent::validate();
        }
        catch ( IlluminateValidationException $e ) {
            throw new InspectionException(
                [ 'Ошибка проверки инварианта', $this ],
                $e
            );
        }

        return $this;
    }


    public function all() : array
    {
        if (! $this->messages) {
            $this->passes();
        }

        $allAttributes = $this->allAttributes();

        $all = []
            + array_intersect_key($this->data, $allAttributes)
            + array_fill_keys(array_keys($allAttributes), null);

        return $all;
    }

    public function allAttributes() : array
    {
        return []
            + array_fill_keys(array_keys($this->data), true)
            + $this->invalidAttributes();
    }


    public function validated() : array
    {
        /** @see parent::validated() */

        if (! $this->messages) {
            $this->passes();
        }

        $validatedAttributes = $this->validatedAttributes();

        $validated = []
            + array_intersect_key($this->data, $validatedAttributes)
            + array_fill_keys(array_keys($validatedAttributes), null);

        return $validated;
    }

    public function validatedAttributes() : array
    {
        if (! $this->messages) {
            $this->passes();
        }

        $validAttributes = $this->validAttributes();

        $validatedAttributes = $validAttributes;

        foreach ( $this->rules as $attr => $rule ) {
            $_attr = explode('.', $attr)[ 0 ];

            $validatedAttributes[ $_attr ] = array_key_exists($_attr, $this->data);
        }

        return $validatedAttributes;
    }


    public function valid() : array
    {
        /** @see parent::valid() */

        if (! $this->messages) {
            $this->passes();
        }

        $validAttributes = $this->validAttributes();

        $valid = array_intersect_key($this->data, $validAttributes);

        return $valid;
    }

    public function validAttributes() : array
    {
        if (! $this->messages) {
            $this->passes();
        }

        $validAttributes = [];

        $invalidAttributes = $this->invalidAttributes();

        foreach ( $this->data as $attr => $value ) {
            if (! isset($invalidAttributes[ $attr ])) {
                $validAttributes[ $attr ] = array_key_exists($attr, $this->data);;
            }
        }

        return $validAttributes;
    }


    public function invalid() : array
    {
        /** @see parent::invalid() */

        if (! $this->messages) {
            $this->passes();
        }

        $invalidAttributes = $this->invalidAttributes();

        $invalid = []
            + array_intersect_key($this->data, $invalidAttributes)
            + array_fill_keys(array_keys($invalidAttributes), null);

        // > gzhegow, removed, no-use-case
        // $theArr = Lib::arr();
        // $invalidDataDot = $theArr->dot($invalidData);
        // $failedRulesDotKeys = array_keys($this->failedRules);
        // $failedDataDot = $theArr->keep_keys($invalidDataDot, $failedRulesDotKeys);
        // $failedData = $theArr->undot($failedDataDot);

        return $invalid;
    }

    public function invalidAttributes() : array
    {
        if (! $this->messages) {
            $this->passes();
        }

        $invalidAttributes = [];

        $messagesArray = $this->messages->toArray();

        foreach ( $messagesArray as $attr => $message ) {
            $_attr = explode('.', $attr)[ 0 ];

            $invalidAttributes[ $_attr ] = array_key_exists($_attr, $this->data);
        }

        return $invalidAttributes;
    }


    public function hasRuleInstance(string $key) : ?RuleInterface
    {
        return $this->registryOfRules[ $key ] ?? null;
    }

    public function getRuleInstance(string $key) : RuleInterface
    {
        return $this->registryOfRules[ $key ];
    }

    /**
     * @return static
     */
    public function registerRuleInstance(string $key, RuleInterface $rule)
    {
        $this->registryOfRules[ $key ] = $rule;

        return $this;
    }


    public function hasReplacerInstance(string $key) : ?ReplacerInterface
    {
        return $this->registryOfReplacers[ $key ] ?? null;
    }

    public function getReplacerInstance(string $key) : ReplacerInterface
    {
        return $this->registryOfReplacers[ $key ];
    }

    /**
     * @return static
     */
    public function registerReplacerInstance(string $key, ReplacerInterface $replacer)
    {
        $this->registryOfReplacers[ $key ] = $replacer;

        return $this;
    }


    /**
     * > laravel всегда считает пустые строки отсутствующими значениями. Это было сделано для HTML-форм
     * > в самом фреймворке из коробки установлено EmptyStringToNullMiddleware
     * > в этом пакете такое поведение отключено
     * > ->modeWeb() и ->modeApi() удаляют пустые строки и NULL из данных, будто их не было
     */
    protected function presentOrRuleIsImplicit($rule, $attribute, $value)
    {
        /** @see parent::presentOrRuleIsImplicit() */

        // if (is_string($value) && trim($value) === '') {
        //     return $this->isImplicit($rule);
        // }

        return false
            || $this->validatePresent($attribute, $value)
            || $this->isImplicit($rule);
    }

    protected function isNotNullIfMarkedAsNullable($rule, $attribute)
    {
        /** @see parent::isNotNullIfMarkedAsNullable() */

        if ($this->isImplicit($rule)) {
            // > если это правило помечено как Implicit, то поле считается переданным
            return true;
        }

        if (! $this->hasRule($attribute, [ 'Nullable' ])) {
            // > если в правилах поля нет Nullable, то поле считается переданным
            return true;
        }

        $value = IlluminateSupportArr::get($this->data, $attribute);

        if (null !== $value) {
            return true;
        }

        return false;
    }

    public function validateRequired($attribute, $value)
    {
        /** @see parent::validateRequired() */
        /** @see parent::validatePresent() */

        // if (is_null($value)) {
        //     return false;
        //
        // } elseif (is_string($value) && trim($value) === '') {
        //     return false;
        // }

        if (is_null($value)) {
            if (IlluminateSupportArr::has($this->data, $attribute)) {
                return true;
            }

            return false;

        } elseif (is_array($value)) {
            if (count($value) > 0) {
                return true;
            }

            return false;

        } elseif (is_object($value)) {
            if ($value instanceof File) {
                $filepath = (string) $value->getPath();

                if ('' === $filepath) {
                    return false;
                }

            } else {
                $count = Lib::php()->count($value);

                if ($count === null) {
                    return true;
                }

                if ($count > 0) {
                    return true;
                }

                return false;
            }
        }

        return true;
    }


    /**
     * > подгружать переводы в память только если установлена локаль валидации, иначе возвращать ключ
     */
    public function addFailure($attribute, $rule, $parameters = [])
    {
        /** @see parent::addFailure() */

        if (! $this->messages) {
            $this->passes();
        }

        $attributeWithPlaceholders = $attribute;
        $attributeWithoutPlaceholders = $this->replacePlaceholderInString(
            $attribute
        );

        if (in_array($rule, $this->excludeRules)) {
            $this->excludeAttribute($attributeWithoutPlaceholders);

            return;
        }

        $message = $this->getMessage(
            $attributeWithPlaceholders,
            $rule
        );

        $message = $this->makeReplacements(
            $message, $attribute, $rule, $parameters
        );

        $this->messages->add($attribute, $message);

        $this->failedRules[ $attribute ][ $rule ] = $parameters;
    }

    protected function getMessage($attribute, $rule)
    {
        /** @see parent::getMessage() */

        $attributeWithPlaceholders = $attribute;
        $attributeWithoutPlaceholders = $this->replacePlaceholderInString(
            $attribute
        );

        $inlineMessage = $this->getInlineMessage(
            $attributeWithoutPlaceholders, $rule
        );
        if (null !== $inlineMessage) {
            return $inlineMessage;
        }

        $lowerRule = IlluminateSupportStr::snake($rule);

        $normalKey = "validation.{$lowerRule}";
        if (null === $this->locale) {
            return $normalKey;
        }

        $customKey = "validation.custom.{$attributeWithoutPlaceholders}.{$lowerRule}";
        $customMessage = $this->translator->get($customKey);

        $hasCustomMessage = ($customMessage !== $customKey);
        if ($hasCustomMessage) {
            return $customMessage;
        }

        /**
         * > gzhegow, laravel does local magic at global layer, NICE!
         */
        $isSizeRule = in_array($rule, $this->sizeRules);
        if ($isSizeRule) {
            $sizeType = $this->getAttributeType($attributeWithPlaceholders);

            $sizeKey = "validation.{$lowerRule}.{$sizeType}";
            $sizeMessage = $this->translator->get($customKey);

            return $sizeMessage ?: $sizeKey;
        }

        $normalMessage = $this->translator->get($normalKey);

        $hasNormalMessage = ($normalKey !== $normalMessage);
        if ($hasNormalMessage) {
            return $normalMessage;
        }

        $localMessage = $this->getFromLocalArray(
            $attributeWithoutPlaceholders,
            $lowerRule,
            $this->fallbackMessages
        );
        if (null !== $localMessage) {
            return $localMessage;
        }

        return $normalKey;
    }

    public function getDisplayableAttribute($attribute)
    {
        /** @see parent::getDisplayableAttribute() */

        $primaryAttribute = $this->getPrimaryAttribute($attribute);

        $expectedAttributes = [];
        $expectedAttributes[ $attribute ] = true;
        $expectedAttributes[ $primaryAttribute ] = true;

        $_attribute = null;

        if (null === $_attribute) {
            foreach ( $expectedAttributes as $expectedAttribute => $bool ) {
                $_attribute = null
                    ?? $this->customAttributes[ $expectedAttribute ]
                    ?? $this->getAttributeFromTranslations($expectedAttribute);

                if (null !== $_attribute) {
                    break;
                }
            }
        }

        if (null === $_attribute) {
            $hasImplicitAttribute = isset($this->implicitAttributes[ $primaryAttribute ]);

            if ($hasImplicitAttribute) {
                $formatter = $this->implicitAttributesFormatter;

                $_attribute = $formatter
                    ? $formatter($attribute)
                    : $attribute;
            }
        }

        if (null === $_attribute) {
            $_attribute = $primaryAttribute;
        }

        return '`' . $_attribute . '`';
    }


    /**
     * @return class-string|callable
     */
    protected function getExtension(string $name)
    {
        if (! isset($this->extensions[ $name ])) {
            throw new RuntimeException('Missing extension: ' . $name);
        }

        return $this->extensions[ $name ];
    }

    /**
     * @return bool
     */
    protected function callExtension($rule, $parameters)
    {
        /** @see parent::callExtension() */

        /**
         * @var ValidatorInterface $validator
         */

        $ruleString = (string) $rule;

        $extensionName = $ruleString;
        $extension = $this->getExtension($extensionName);

        $extensionParameters = array_values($parameters);

        $ruleClass = $extension;
        $ruleParameters = $extensionParameters[ 2 ];

        $attribute = $extensionParameters[ 0 ];
        $value = $extensionParameters[ 1 ];
        $validator = $extensionParameters[ 3 ];

        $extensionParameters[ 'rule' ] = $ruleString;
        $extensionParameters[ 'ruleClass' ] = $ruleClass;
        $extensionParameters[ 'ruleParameters' ] = $ruleParameters;

        $extensionParameters[ 'attribute' ] = $attribute;
        $extensionParameters[ 'value' ] = $value;
        $extensionParameters[ 'validator' ] = $validator;

        $ruleInstanceKey = md5(serialize([ $attribute, $ruleString, $ruleParameters ]));

        if (! $instance = $validator->hasRuleInstance($ruleInstanceKey)) {
            $instance = $this->factory->newRule($extension);

            $validator->registerRuleInstance($ruleInstanceKey, $instance);
        }

        $fn = [ $instance, 'validate' ];
        $fnArguments = $extensionParameters;

        $result = $this->processor->callExtension($fn, $fnArguments);

        return $result;
    }

    /**
     * @deprecated
     */
    protected function callClassBasedExtension($callback, $parameters)
    {
        /** @see parent::callClassBasedExtension() */

        throw new RuntimeException('This method is deprecated');
    }


    /**
     * @return class-string|callable
     */
    protected function getReplacer(string $name)
    {
        if (! isset($this->replacers[ $name ])) {
            throw new RuntimeException('Missing replacer: ' . $name);
        }

        return $this->replacers[ $name ];
    }

    protected function callReplacer($message, $attribute, $rule, $parameters, $validator)
    {
        /** @see parent::callReplacer() */

        /**
         * @var ValidatorInterface $validator
         */

        $ruleString = (string) $rule;
        $ruleParameters = $parameters;

        $replacerName = $ruleString;
        $replacer = $this->getReplacer($replacerName);

        $replacerParameters = [
            $message,
            $attribute,
            $rule,
            $parameters,
            $validator,
        ];

        $replacerParameters[ 'rule' ] = $ruleString;
        $replacerParameters[ 'ruleParameters' ] = $ruleParameters;

        $replacerParameters[ 'message' ] = $message;
        $replacerParameters[ 'attribute' ] = $attribute;
        $replacerParameters[ 'validator' ] = $validator;

        $replacerInstanceKey = md5(serialize([ $attribute, $ruleString, $ruleParameters ]));

        if (! $instance = $validator->hasReplacerInstance($replacerInstanceKey)) {
            $instance = $this->factory->newReplacer($replacer);

            $validator->registerReplacerInstance($replacerInstanceKey, $instance);
        }

        $fn = [ $instance, 'replace' ];
        $fnArguments = $replacerParameters;

        $result = $this->processor->callReplacer($fn, $fnArguments);

        return $result;
    }

    protected function callClassBasedReplacer($callback, $message, $attribute, $rule, $parameters, $validator)
    {
        /** @see parent::callClassBasedReplacer() */

        throw new RuntimeException('This method is deprecated');
    }
}
