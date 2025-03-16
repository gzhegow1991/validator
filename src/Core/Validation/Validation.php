<?php

namespace Gzhegow\Validator\Core\Validation;

use Gzhegow\Lib\Lib;
use Illuminate\Validation\Validator;
use Gzhegow\Validator\ValidatorFactory;
use Gzhegow\Validator\Core\Rule\RuleInterface;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\ValidatorFactoryInterface;
use Gzhegow\Validator\Exception\Runtime\ValidationException;
use Gzhegow\Validator\Exception\Runtime\InspectionException;
use Gzhegow\Validator\Package\Illuminate\Validation\ValidatorInterface;
use Illuminate\Contracts\Support\MessageBag as IlluminateMessageBagContract;
use Illuminate\Contracts\Validation\Factory as IlluminateValidatorFactoryContract;


class Validation implements ValidationInterface
{
    /**
     * @var ValidatorFactory
     */
    protected $factory;

    /**
     * @var IlluminateValidatorFactoryContract
     */
    protected $illuminateValidatorFactory;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var bool
     */
    protected $modeWeb;
    /**
     * @var bool
     */
    protected $modeApi;

    /**
     * @var int
     */
    protected $id = 0;

    /**
     * @var array<int, array|object>
     */
    protected $data = [];
    /**
     * @var array<int, array>
     */
    protected $rules = [];
    /**
     * @var array<int, string[]>
     */
    protected $messages = [];
    /**
     * @var array<int, string[]>
     */
    protected $attributes = [];
    /**
     * @var array<int, array<string, callable|callable[]>>
     */
    protected $filters = [];

    /**
     * @var array|object
     */
    protected $dataMerged;
    /**
     * @var array<string, array>
     */
    protected $rulesMerged;
    /**
     * @var array<string, string>
     */
    protected $messagesMerged;
    /**
     * @var array<string, string>
     */
    protected $attributesMerged;
    /**
     * @var array<string, callable|callable[]>
     */
    protected $filtersMerged;

    /**
     * @var ValidatorInterface
     */
    protected $illuminateValidator;


    public function __construct(
        ValidatorFactoryInterface $factory,
        //
        IlluminateValidatorFactoryContract $illuminateValidatorFactory
    )
    {
        $this->factory = $factory;

        $this->illuminateValidatorFactory = $illuminateValidatorFactory;
    }


    protected function buildIlluminateValidator() : Validator
    {
        $_data = [];
        foreach ( $this->data as $dataItem ) {
            $_data = array_replace($_data, $dataItem);
        }

        $_rules = [];
        foreach ( $this->rules as $rulesItem ) {
            foreach ( $rulesItem as $key => $rulesList ) {
                $rulesList = Lib::php()->to_list($rulesList);

                $_rulesList = [];
                foreach ( $rulesList as $rulesListItem ) {
                    if (is_string($rulesListItem)) {
                        $_rulesList = array_merge(
                            $_rulesList,
                            explode('|', $rulesListItem)
                        );

                    } else {
                        $_rulesList[] = $rulesListItem;
                    }
                }

                $_rules[ $key ] = $_rules[ $key ] ?? [];

                $_rules[ $key ] = array_merge(
                    $_rules[ $key ],
                    $_rulesList
                );
            }
        }

        $_messages = [];
        foreach ( $this->messages as $messagesItem ) {
            $_messages = array_replace(
                $_messages,
                $messagesItem
            );
        }

        $_attributes = [];
        foreach ( $this->attributes as $attributesItem ) {
            $_attributes = array_replace(
                $_attributes,
                $attributesItem
            );
        }

        $_filters = [];
        foreach ( $this->filters as $filters ) {
            foreach ( $filters as $key => $list ) {
                $_filters[ $key ] = array_merge(
                    $_filters[ $key ] ?? [],
                    $list
                );
            }
        }

        if ($this->modeApi || $this->modeWeb) {
            $theArr = Lib::arr();

            foreach ( $theArr->walk_it($_data) as $path => $value ) {
                if (false
                    || ($this->modeApi && (null === $value))
                    || ($this->modeWeb && ('' === $value))
                ) {
                    $theArr->unset_path($_data, $path);
                }
            }
        }

        $this->dataMerged = $_data;
        $this->rulesMerged = $_rules;
        $this->messagesMerged = $_messages;
        $this->attributesMerged = $_attributes;
        $this->filtersMerged = $_filters;

        /**
         * @var ValidatorInterface $illuminateValidator
         */
        $illuminateValidator = $this->illuminateValidatorFactory->make(
            $_data,
            $_rules,
            $_messages,
            $_attributes
        );

        if (null !== $this->locale) {
            $illuminateValidator->setLocale($this->locale);
        }

        return $illuminateValidator;
    }

    public function getIlluminateValidator() : ValidatorInterface
    {
        return $this->illuminateValidator;
    }


    public function merge(ValidationInterface $validation)
    {
        for ( $i = 0; $i < $validation->id; $i++ ) {
            if (isset($validation->data[ $i ])) {
                $this->data[ $this->id++ ] = $validation->data[ $i ];

            } elseif (isset($validation->rules[ $i ])) {
                $this->rules[ $this->id++ ] = $validation->rules[ $i ];

            } elseif (isset($validation->filters[ $i ])) {
                $this->filters[ $this->id++ ] = $validation->filters[ $i ];

            } elseif (isset($validation->messages[ $i ])) {
                $this->messages[ $this->id++ ] = $validation->messages[ $i ];

            } elseif (isset($validation->attributes[ $i ])) {
                $this->attributes[ $this->id++ ] = $validation->attributes[ $i ];

            } else {
                continue;
            }

            $this->illuminateValidator = null;
        }

        return $this;
    }


    /**
     * @throws ValidationException
     * @noinspection PhpDocMissingThrowsInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function validate(&$bind = null) : array
    {
        if (! $this->illuminateValidator) {
            $this->illuminateValidator = $this->buildIlluminateValidator();
        }

        $this->illuminateValidator->validate();

        $result = $this->validated($bind);

        return $result;
    }

    /**
     * @throws InspectionException
     * @noinspection PhpDocMissingThrowsInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function inspect(&$bind = null) : array
    {
        if (! $this->illuminateValidator) {
            $this->illuminateValidator = $this->buildIlluminateValidator();
        }

        $this->illuminateValidator->inspect();

        $result = $this->validated($bind);

        return $result;
    }


    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function validated(&$bind = null) : array
    {
        if (! $this->illuminateValidator) {
            $this->illuminateValidator = $this->buildIlluminateValidator();
        }

        $valid = $this->illuminateValidator->valid();

        $valid = $this->processFilters($valid);

        $resultAttributes = $this->validatedAttributes();

        $result = []
            + $valid
            + array_fill_keys(array_keys($resultAttributes), null);

        if (null !== $bind) {
            $this->processBind($bind, $result);
        }

        return $result;
    }

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function validatedAttributes() : array
    {
        if (! $this->illuminateValidator) {
            $this->illuminateValidator = $this->buildIlluminateValidator();
        }

        $resultAttributes = $this->illuminateValidator->validatedAttributes();

        return $resultAttributes;
    }


    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function valid(&$bind = null) : array
    {
        if (! $this->illuminateValidator) {
            $this->illuminateValidator = $this->buildIlluminateValidator();
        }

        $valid = $this->illuminateValidator->valid();

        $valid = $this->processFilters($valid);

        $result = $valid;

        if (null !== $bind) {
            $this->processBind($bind, $result);
        }

        return $result;
    }

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function validAttributes() : array
    {
        if (! $this->illuminateValidator) {
            $this->illuminateValidator = $this->buildIlluminateValidator();
        }

        $resultAttributes = $this->illuminateValidator->validAttributes();

        return $resultAttributes;
    }


    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function invalid(&$bind = null) : array
    {
        if (! $this->illuminateValidator) {
            $this->illuminateValidator = $this->buildIlluminateValidator();
        }

        $result = $this->illuminateValidator->invalid();

        if (null !== $bind) {
            $this->processBind($bind, $result);
        }

        return $result;
    }

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function invalidAttributes() : array
    {
        if (! $this->illuminateValidator) {
            $this->illuminateValidator = $this->buildIlluminateValidator();
        }

        $resultAttributes = $this->illuminateValidator->invalidAttributes();

        return $resultAttributes;
    }


    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function messageBag() : IlluminateMessageBagContract
    {
        if (! $this->illuminateValidator) {
            $this->illuminateValidator = $this->buildIlluminateValidator();
        }

        $messageBag = $this->illuminateValidator->messages();

        return $messageBag;
    }

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function messages() : array
    {
        if (! $this->illuminateValidator) {
            $this->illuminateValidator = $this->buildIlluminateValidator();
        }

        $messageBag = $this->illuminateValidator->messages();

        return $messageBag->getMessages();
    }


    /**
     * @return static
     */
    public function modeApi(bool $modeApi = null)
    {
        // > режим подразумевает, что запрос получен из API
        // > все NULL, оказавшиеся в данных, будут удалены, будто их не передавали

        $modeApi = $modeApi ?? true;

        $last = $this->modeApi;

        if ($last !== $modeApi) {
            $this->illuminateValidator = null;
        }

        $this->modeApi = $modeApi;

        return $this;
    }

    /**
     * @return static
     */
    public function modeWeb(bool $modeWeb = null)
    {
        // > режим подразумевает, что запрос получен из HTML-формы
        // > все пустые строки, оказавшиеся в данных, будут удалены, будто их не передавали

        $modeWeb = $modeWeb ?? true;

        $last = $this->modeWeb;

        if ($last !== $modeWeb) {
            $this->illuminateValidator = null;
        }

        $this->modeWeb = $modeWeb;

        return $this;
    }


    /**
     * @return static
     */
    public function addData(array $data)
    {
        if (! count($data)) {
            return $this;
        }

        $this->pushData($data);

        return $this;
    }


    /**
     * @param array<string|RuleInterface>|string|RuleInterface $rules
     * @param callable                                         ...$filters
     *
     * @return static
     */
    public function addRules(string $key, $rules, ...$filters)
    {
        if (! (false
            || (null === $rules)
            || ('' === $rules)
            || ([] === $rules)
        )) {
            $rulesList = Lib::php()->to_list($rules);

            $this->pushRules([ $key => $rulesList ]);
        }

        if (count($filters)) {
            $this->pushFilters([ $key => $filters ]);
        }

        return $this;
    }

    /**
     * @param array<string|RuleInterface>|string|RuleInterface $rules
     * @param array<string, callable[]>                        $filters
     * @param array<string, string>                            $messages
     * @param array<string, string>                            $attributes
     *
     * @return static
     */
    public function addRulesMap(
        array $rules = [],
        array $filters = [],
        array $messages = [],
        array $attributes = []
    )
    {
        if (count($rules)) {
            $this->pushRules($rules);
        }
        if (count($filters)) {
            $this->pushFilters($filters);
        }
        if (count($messages)) {
            $this->pushMessages($messages);
        }
        if (count($attributes)) {
            $this->pushAttributes($attributes);
        }

        return $this;
    }


    /**
     * @return static
     */
    public function setLocale(?string $locale)
    {
        $last = $this->locale;

        if ($last !== $locale) {
            $this->illuminateValidator = null;
        }

        $this->locale = $locale;

        return $this;
    }


    protected function pushData(array $data)
    {
        $this->data[ $this->id++ ] = $data;

        $this->illuminateValidator = null;

        return $this;
    }

    protected function pushRules(array $rules)
    {
        [ , $rulesDict ] = Lib::arr()->kwargs($rules);

        if (! count($rulesDict)) {
            return $this;
        }

        $this->rules[ $this->id++ ] = $rulesDict;

        $this->illuminateValidator = null;

        return $this;
    }

    protected function pushFilters(array $filters)
    {
        [ , $filtersDict ] = Lib::arr()->kwargs($filters);

        if (! count($filtersDict)) {
            return $this;
        }

        foreach ( $filtersDict as $filtersArray ) {
            foreach ( $filtersArray as $i => $filter ) {
                if (! is_callable($filter)) {
                    throw new LogicException(
                        [ 'Each of `filters` should be callable', $filter, $i ]
                    );
                }
            }
        }

        $this->filters[ $this->id++ ] = $filtersDict;

        $this->illuminateValidator = null;

        return $this;
    }

    protected function pushMessages(array $messages)
    {
        [ , $messagesDict ] = Lib::arr()->kwargs($messages);

        if (! count($messagesDict)) {
            return $this;
        }

        $this->messages[ $this->id++ ] = $messagesDict;

        $this->illuminateValidator = null;

        return $this;
    }

    protected function pushAttributes(array $attributes)
    {
        [ , $attributesDict ] = Lib::arr()->kwargs($attributes);

        if (! count($attributesDict)) {
            return $this;
        }

        $this->attributes[ $this->id++ ] = $attributesDict;

        $this->illuminateValidator = null;

        return $this;
    }


    protected function processFilters(array $data)
    {
        $theArr = Lib::arr();
        $theItertools = Lib::itertools();

        $genWalk = $theArr->walk_it($data, _ARR_WALK_WITH_EMPTY_ARRAYS | _ARR_WALK_WITH_PARENTS);

        foreach ( $genWalk as $path => $value ) {
            $genMask = $theItertools->product_repeat_it(
                count($path),
                [ null, true ]
            );

            $isValueArray = is_array($value);

            $filtersAll = [];
            foreach ( $genMask as $mask ) {
                $possiblePath = [];
                foreach ( $mask as $i => $v ) {
                    $possiblePath[ $i ] = (null === $v)
                        ? $path[ $i ]
                        : '*';
                }

                $isLastAsterisk = (end($possiblePath) === '*');

                if ($isLastAsterisk && $isValueArray) {
                    continue;
                }

                $possibleKey = implode('.', $possiblePath);

                if (isset($this->filtersMerged[ $possibleKey ])) {
                    $filtersAll = array_merge(
                        $filtersAll,
                        $this->filtersMerged[ $possibleKey ]
                    );
                }
            }

            $current = $value;
            foreach ( $filtersAll as $filter ) {
                $current = call_user_func($filter, $current);
            }

            $theArr->set_path($data, $path, $current);
        }

        return $data;
    }

    protected function processBind(&$bind, array $data)
    {
        $isArray = is_array($bind);
        $isObject = is_object($bind);

        if (! ($isArray || $isObject)) {
            throw new LogicException(
                'The `reference` should be array or object'
            );
        }

        $boundArray = null;
        $boundObject = null;

        $isArray
            ? ($boundArray =& $bind)
            : ($boundObject =& $bind);

        if ($boundArray) {
            foreach ( $data as $key => $value ) {
                $boundArray[ $key ] = $value;
            }

        } elseif ($boundObject) {
            foreach ( $data as $key => $value ) {
                $boundObject->{$key} = $value;
            }
        }
    }
}
