<?php

namespace Gzhegow\Validator\Validation;

use Gzhegow\Lib\Lib;
use Gzhegow\Lib\Modules\Arr\ArrPath;
use Gzhegow\Validator\Rule\GenericRule;
use Gzhegow\Validator\Rule\RuleInterface;
use Gzhegow\Validator\Filter\GenericFilter;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\ValidatorFactoryInterface;
use Gzhegow\Validator\Exception\RuntimeException;
use Gzhegow\Validator\RuleRegistry\RuleRegistryInterface;
use Gzhegow\Validator\Processor\ValidatorProcessorInterface;
use Gzhegow\Validator\Exception\Runtime\InspectionException;
use Gzhegow\Validator\Exception\Runtime\ValidationException;
use Gzhegow\Validator\Rule\Kit\Implicit\RuleImplicitInterface;
use Gzhegow\Validator\Translator\ValidatorTranslatorInterface;


class Validation implements ValidationInterface
{
    /**
     * > Валидации поддерживают строчную запись для правил/проверок
     *
     * > Правила в строке задаются для того, чтобы потом их в таком же "красивом виде" отдать на фронтенд
     * > но это менее "правильно" с точки зрения ООП и управления проектами, где нужно избежать ручной регистрации правил
     *
     * > Символ `,` не используется валидатором, но если один параметр содержит под-параметры,
     * > то их можно разделить их через `,` или любым другим символом, разобрать их можно в MyRule::parse()
     */

    /**
     * > правила для быстрого поиска хранятся в индексированном массиве, где путь разделен NUL-байтами
     */
    const SYMBOL_NUL = "\0";

    /**
     * > если задавать правила в строке, то несколько правил можно разделять символом `|`
     */
    const SYMBOL_RULELIST_SEPARATOR = '|';

    /**
     * > символ `:` используется для указания аргументов правил, например, `unique:true` - первый аргумент $isStrict = true
     */
    const SYMBOL_RULEARGS_SEPARATOR = ':';
    /**
     * > символ `;` используется для разделения нескольких параметров `in_array:1,2,3;true` - в массиве [1,2,3] и $isStrict = true
     */
    const SYMBOL_RULEARGS_DELIMITER = ';';

    /**
     * > вы можете задавать правила для вложенных ключей например `path.to.key`
     */
    const SYMBOL_DOTPATH_SEPARATOR = '.';
    /**
     * > в регистрации правил на ключи можно использовать символ `*`, чтобы обработать все значения массива одинаково
     */
    const SYMBOL_DOTPATH_WILDCARD_SEQUENCE = '*';

    /**
     * > некоторые правила зависят от других ключей, к ним можно обращаться через `path/to/key`
     */
    const SYMBOL_FIELDPATH_PARENT = '.';
    /**
     * > некоторые правила зависят от других ключей, к ним можно обращаться через `../../path/to/key`
     */
    const SYMBOL_FIELDPATH_SEPARATOR = '/';

    /**
     * > во время процессинга значение в dataTypes[] меняет свой тип
     */
    const VALUE_TYPE_ORIGINAL = 1;
    const VALUE_TYPE_FILTERED = 2;
    const VALUE_TYPE_DEFAULT  = 3;


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

    /**
     * @var bool
     */
    protected $modeApi = false;
    /**
     * @var bool
     */
    protected $modeWeb = false;

    /**
     * @var bool
     */
    protected $isBuilt = false;
    /**
     * @var bool
     */
    protected $isProcessed = false;

    /**
     * @var int
     */
    protected $queueId = 0;

    /**
     * @var array<int, array>
     */
    protected $dataQueue = [];
    /**
     * @var array<int, array<string, GenericFilter>>
     */
    protected $filtersQueue = [];
    /**
     * @var array<int, array<string, GenericRule>
     */
    protected $rulesQueue = [];
    /**
     * @var array<int, array<string, array{ 0?: mixed }>>
     */
    protected $defaultsQueue = [];

    /**
     * @var array
     */
    protected $dataMerged;
    /**
     * @var array<string, GenericFilter[]>
     */
    protected $filtersMerged;
    /**
     * @var array<string, GenericRule[]>
     */
    protected $rulesMerged;
    /**
     * @var array<string, mixed>
     */
    protected $defaultsMerged;

    /**
     * @var array
     */
    protected $data;
    /**
     * @var array<string, string[]>
     */
    protected $dataPathes;
    /**
     * @var array<string, int>
     */
    protected $dataTypes;

    /**
     * @var array<string, mixed>
     */
    protected $dataIndex;
    /**
     * @var array<string, mixed>
     */
    protected $dataMergedIndex;

    /**
     * @var array<string, RuleInterface[]>
     */
    protected $rulesByKeyNulpath;
    /**
     * @var array<string, array[]>
     */
    protected $errorsByKeyNulpath;
    /**
     * @var array<string, string[]>
     */
    protected $messagesByKeyNulpath;

    /**
     * @var array<string, array<string, bool>>
     */
    protected $cacheMatchKeyDotpathesByWildcardDotpath;


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


    /**
     * @return static
     */
    public function merge(ValidationInterface $validation)
    {
        for ( $i = 0; $i < $validation->queueId; $i++ ) {
            if (isset($validation->dataQueue[ $i ])) {
                $this->dataQueue[ $this->queueId++ ] = $validation->dataQueue[ $i ];

            } elseif (isset($validation->rulesQueue[ $i ])) {
                $this->rulesQueue[ $this->queueId++ ] = $validation->rulesQueue[ $i ];

            } elseif (isset($validation->filtersQueue[ $i ])) {
                $this->filtersQueue[ $this->queueId++ ] = $validation->filtersQueue[ $i ];

            } elseif (isset($validation->defaultsQueue[ $i ])) {
                $this->defaultsQueue[ $this->queueId++ ] = $validation->defaultsQueue[ $i ];

            } else {
                continue;
            }

            $this->isBuilt = false;
            $this->isProcessed = false;
        }

        return $this;
    }


    public function has($path, &$value = null) : bool
    {
        $value = null;

        if (! Lib::type()->arrpath($keyPath, $path)) {
            throw new LogicException(
                [ 'The `path` should be valid path', $path ]
            );
        }

        $keyPath = $keyPath->getPath();
        $keyNulpath = static::SYMBOL_NUL . implode(static::SYMBOL_NUL, $keyPath);

        $status = $this->hasByIndex($keyNulpath, $value);

        return $status;
    }

    /**
     * @return mixed
     */
    public function get($path, array $fallback = [])
    {
        $status = $this->has($path, $value);

        if (! $status) {
            if ($fallback) {
                [ $fallback ] = $fallback;

                return $fallback;
            }

            throw new RuntimeException(
                [
                    'Missing path in filtered data and defaults',
                    $path,
                ]
            );
        }

        return $value;
    }

    private function hasByIndex(string $nulpath, &$value = null) : bool
    {
        $value = null;

        if (isset($this->dataTypes[ $nulpath ])) {
            $value = $this->dataIndex[ $nulpath ];

            return true;
        }

        return false;
    }


    public function hasFiltered($path, &$value = null) : bool
    {
        $value = null;

        if (! Lib::type()->arrpath($keyPath, $path)) {
            throw new LogicException(
                [ 'The `path` should be valid path', $path ]
            );
        }

        $keyPath = $keyPath->getPath();
        $keyNulpath = static::SYMBOL_NUL . implode(static::SYMBOL_NUL, $keyPath);

        $status = $this->hasFilteredByIndex($keyNulpath, $value);

        return $status;
    }

    /**
     * @return mixed
     */
    public function getFiltered($path, array $fallback = [])
    {
        $status = $this->hasFiltered($path, $value);

        if (! $status) {
            if ($fallback) {
                [ $fallback ] = $fallback;

                return $fallback;
            }

            throw new RuntimeException(
                [
                    'Missing path in filtered data and defaults',
                    $path,
                ]
            );
        }

        return $value;
    }

    private function hasFilteredByIndex(string $nulpath, &$value = null) : bool
    {
        $value = null;

        $valueType = $this->dataTypes[ $nulpath ] ?? 0;

        if (static::VALUE_TYPE_FILTERED === $valueType) {
            $value = $this->dataIndex[ $nulpath ];

            return true;
        }

        return false;
    }


    public function hasDefault($path, &$value = null) : bool
    {
        $value = null;

        if (! Lib::type()->arrpath($keyPath, $path)) {
            throw new LogicException(
                [ 'The `path` should be valid path', $path ]
            );
        }

        $keyPath = $keyPath->getPath();
        $keyNulpath = static::SYMBOL_NUL . implode(static::SYMBOL_NUL, $keyPath);

        $status = $this->hasDefaultByIndex($keyNulpath, $value);

        return $status;
    }

    /**
     * @return mixed
     */
    public function getDefault($path, array $fallback = [])
    {
        $status = $this->hasDefault($path, $value);

        if (! $status) {
            if ($fallback) {
                [ $fallback ] = $fallback;

                return $fallback;
            }

            throw new RuntimeException(
                [
                    'Missing path in filtered data and defaults',
                    $path,
                ]
            );
        }

        return $value;
    }

    private function hasDefaultByIndex(string $nulpath, &$value = null) : bool
    {
        $value = null;

        $valueType = $this->dataTypes[ $nulpath ] ?? 0;

        if (static::VALUE_TYPE_DEFAULT === $valueType) {
            $value = $this->dataIndex[ $nulpath ];

            return true;
        }

        return false;
    }


    public function hasOriginal($path, &$value = null) : bool
    {
        $value = null;

        if (! Lib::type()->arrpath($keyPath, $path)) {
            throw new LogicException(
                [ 'The `path` should be valid path', $path ]
            );
        }

        $keyPath = $keyPath->getPath();
        $keyNulpath = static::SYMBOL_NUL . implode(static::SYMBOL_NUL, $keyPath);

        $status = $this->hasOriginalByIndex($keyNulpath, $value);

        return $status;
    }

    /**
     * @return mixed
     */
    public function getOriginal($path, array $fallback = [])
    {
        $status = $this->hasOriginal($path, $value);

        if (! $status) {
            if ($fallback) {
                [ $fallback ] = $fallback;

                return $fallback;
            }

            throw new RuntimeException(
                [
                    'Missing path in filtered data and defaults',
                    $path,
                ]
            );
        }

        return $value;
    }

    private function hasOriginalByIndex(string $nulpath, &$value = null) : bool
    {
        $value = null;

        $valueType = $this->dataTypes[ $nulpath ] ?? 0;

        if (static::VALUE_TYPE_ORIGINAL === $valueType) {
            $value = $this->dataIndex[ $nulpath ];

            return true;
        }

        return false;
    }


    /**
     * @throws ValidationException
     */
    public function validate(&$bind = null) : array
    {
        $status = $this->passes();

        if (! $status) {
            throw new ValidationException($this, 'Validation failed');
        }

        $valid = $this->valid();

        if (null !== $bind) {
            $this->applyBind($bind, $valid);
        }

        return $valid;
    }

    /**
     * @throws InspectionException
     */
    public function inspect(&$bind = null) : array
    {
        $status = $this->passes();

        if (! $status) {
            throw new InspectionException($this, 'Inspection failed');
        }

        $valid = $this->valid();

        if (null !== $bind) {
            $this->applyBind($bind, $valid);
        }

        return $valid;
    }


    public function passes() : bool
    {
        if (! $this->isBuilt) {
            $this->build();

            $this->isBuilt = true;
        }

        if (! $this->isProcessed) {
            $this->process();

            $this->isProcessed = true;
        }

        $status = ([] === $this->errorsByKeyNulpath);

        return $status;
    }

    public function errors() : array
    {
        if (! $this->isBuilt) {
            $this->build();

            $this->isBuilt = true;
        }

        if (! $this->isProcessed) {
            $this->process();

            $this->isProcessed = true;
        }

        $errors = [];
        foreach ( $this->errorsByKeyNulpath as $keyNulpath => $array ) {
            $keyPath = explode(
                static::SYMBOL_NUL,
                ltrim($keyNulpath, static::SYMBOL_NUL)
            );

            $keyDotpath = implode('.', $keyPath);

            $errors[ $keyDotpath ] = $array;
        }

        return $errors;
    }


    public function messages() : array
    {
        if (! $this->isBuilt) {
            $this->build();

            $this->isBuilt = true;
        }

        if (! $this->isProcessed) {
            $this->process();

            $this->isProcessed = true;
        }

        $this->messagesTranslate();

        $messages = [];
        foreach ( $this->messagesByKeyNulpath as $keyNulpath => $array ) {
            $keyPath = explode(
                static::SYMBOL_NUL,
                ltrim($keyNulpath, static::SYMBOL_NUL)
            );

            $keyDotpath = implode('.', $keyPath);

            $messages[ $keyDotpath ] = $array;
        }

        return $messages;
    }

    protected function messagesTranslate() : void
    {
        if (null !== $this->messagesByKeyNulpath) {
            return;
        }

        $messagesByKeyNulpath = $this->translator->translate($this->errorsByKeyNulpath);

        $this->messagesByKeyNulpath = $messagesByKeyNulpath;
    }


    public function getRules() : array
    {
        if (! $this->isBuilt) {
            $this->build();

            $this->isBuilt = true;
        }

        $result = [];

        foreach ( $this->rulesMerged as $keyNulpath => $list ) {
            $keyPath = explode(
                static::SYMBOL_NUL,
                ltrim($keyNulpath, static::SYMBOL_NUL)
            );

            $keyDotpath = implode('.', $keyPath);

            $result[ $keyDotpath ] = $list;
        }

        return $result;
    }

    public function rules(array $fillKeys = []) : array
    {
        if (! $this->isBuilt) {
            $this->build();

            $this->isBuilt = true;
        }

        $hasValue = ([] !== $fillKeys);

        $result = [];

        if ($hasValue) {
            foreach ( array_keys($this->rulesMerged) as $keyNulpath ) {
                $keyPath = explode(
                    static::SYMBOL_NUL,
                    ltrim($keyNulpath, static::SYMBOL_NUL)
                );

                $keyDotpath = implode('.', $keyPath);

                $result[ $keyDotpath ] = $fillKeys[ 0 ];
            }

        } else {
            foreach ( $this->rulesMerged as $keyNulpath => $list ) {
                $keyPath = explode(
                    static::SYMBOL_NUL,
                    ltrim($keyNulpath, static::SYMBOL_NUL)
                );

                $keyDotpath = implode('.', $keyPath);

                $list = array_map('strval', $list);
                $list = implode('|', $list);

                $result[ $keyDotpath ] = $list;
            }
        }

        return $result;
    }


    public function data(&$bind = null) : array
    {
        if (! $this->isBuilt) {
            $this->build();

            $this->isBuilt = true;
        }

        $theArr = Lib::arr();

        $result = [];

        $gen = $theArr->walk_it(
            $this->dataMerged,
            _ARR_WALK_WITH_EMPTY_ARRAYS
        );

        foreach ( $gen as $keyPath => $value ) {
            $theArr->set_path(
                $result, $keyPath, $value
            );
        }

        if (null !== $bind) {
            $this->applyBind($bind, $result);
        }

        return $result;
    }

    public function dataAttributes(array $fillKeys = []) : array
    {
        if (! $this->isBuilt) {
            $this->build();

            $this->isBuilt = true;
        }

        $result = Lib::arr()->dot(
            $this->dataMerged, '.', $fillKeys,
            _ARR_WALK_WITH_EMPTY_ARRAYS | _ARR_WALK_WITH_LISTS
        );

        return $result;
    }


    public function dataValidated(&$bind = null) : array
    {
        if (! $this->isBuilt) {
            $this->build();

            $this->isBuilt = true;
        }

        if (! $this->isProcessed) {
            $this->process();

            $this->isProcessed = true;
        }

        $theArr = Lib::arr();

        $result = [];

        foreach ( $this->dataMergedIndex as $keyNulpath => $value ) {
            $hasRules = isset($this->rulesByKeyNulpath[ $keyNulpath ]);

            if (! $hasRules) {
                continue;
            }

            if (is_array($value) && ([] !== $value)) {
                continue;
            }

            $keyPath = $this->dataPathes[ $keyNulpath ];

            $theArr->set_path($result, $keyPath, $value);
        }

        if (null !== $bind) {
            $this->applyBind($bind, $result);
        }

        return $result;
    }

    public function dataValidatedAttributes(array $fillKeys = []) : array
    {
        if (! $this->isBuilt) {
            $this->build();

            $this->isBuilt = true;
        }

        if (! $this->isProcessed) {
            $this->process();

            $this->isProcessed = true;
        }

        $hasValue = ([] !== $fillKeys);

        $result = [];

        foreach ( $this->dataMergedIndex as $keyNulpath => $value ) {
            $hasRules = isset($this->rulesByKeyNulpath[ $keyNulpath ]);

            if (! $hasRules) {
                continue;
            }

            if (is_array($value) && ([] !== $value)) {
                continue;
            }

            $keyPath = $this->dataPathes[ $keyNulpath ];

            $keyDotpath = implode('.', $keyPath);

            $result[ $keyDotpath ] = $hasValue
                ? $fillKeys[ 0 ]
                : $value;
        }

        return $result;
    }


    public function all(&$bind = null) : array
    {
        if (! $this->isBuilt) {
            $this->build();

            $this->isBuilt = true;
        }

        if (! $this->isProcessed) {
            $this->process();

            $this->isProcessed = true;
        }

        $theArr = Lib::arr();

        $result = [];

        $gen = $theArr->walk_it(
            $this->data,
            _ARR_WALK_WITH_EMPTY_ARRAYS
        );

        foreach ( $gen as $keyPath => $value ) {
            $theArr->set_path(
                $result, $keyPath, $value
            );
        }

        if (null !== $bind) {
            $this->applyBind($bind, $result);
        }

        return $result;
    }

    public function allAttributes(array $fillKeys = []) : array
    {
        if (! $this->isBuilt) {
            $this->build();

            $this->isBuilt = true;
        }

        if (! $this->isProcessed) {
            $this->process();

            $this->isProcessed = true;
        }

        $result = Lib::arr()->dot(
            $this->data, '.', $fillKeys,
            _ARR_WALK_WITH_EMPTY_ARRAYS | _ARR_WALK_WITH_LISTS
        );

        return $result;
    }


    public function valid(&$bind = null) : array
    {
        if (! $this->isBuilt) {
            $this->build();

            $this->isBuilt = true;
        }

        if (! $this->isProcessed) {
            $this->process();

            $this->isProcessed = true;
        }

        $theArr = Lib::arr();

        $result = [];

        foreach ( $this->dataIndex as $keyNulpath => $value ) {
            $hasErrors = isset($this->errorsByKeyNulpath[ $keyNulpath ]);

            if ($hasErrors) {
                continue;
            }

            if (is_array($value) && ([] !== $value)) {
                continue;
            }

            $keyPath = $this->dataPathes[ $keyNulpath ];

            $theArr->set_path($result, $keyPath, $value);
        }

        if (null !== $bind) {
            $this->applyBind($bind, $result);
        }

        return $result;
    }

    public function validAttributes(array $fillKeys = []) : array
    {
        if (! $this->isBuilt) {
            $this->build();

            $this->isBuilt = true;
        }

        if (! $this->isProcessed) {
            $this->process();

            $this->isProcessed = true;
        }

        $hasValue = ([] !== $fillKeys);

        $result = [];

        foreach ( $this->dataIndex as $keyNulpath => $value ) {
            $hasErrors = isset($this->errorsByKeyNulpath[ $keyNulpath ]);

            if ($hasErrors) {
                continue;
            }

            if (is_array($value) && ([] !== $value)) {
                continue;
            }

            $keyPath = $this->dataPathes[ $keyNulpath ];

            $keyDotpath = implode('.', $keyPath);

            $result[ $keyDotpath ] = $hasValue
                ? $fillKeys[ 0 ]
                : $value;
        }

        return $result;
    }


    public function invalid(&$bind = null) : array
    {
        if (! $this->isBuilt) {
            $this->build();

            $this->isBuilt = true;
        }

        if (! $this->isProcessed) {
            $this->process();

            $this->isProcessed = true;
        }

        $theArr = Lib::arr();

        $result = [];

        foreach ( $this->dataIndex as $keyNulpath => $value ) {
            $hasErrors = isset($this->errorsByKeyNulpath[ $keyNulpath ]);

            if (! $hasErrors) {
                continue;
            }

            if (is_array($value) && ([] !== $value)) {
                continue;
            }

            $keyPath = $this->dataPathes[ $keyNulpath ];

            $theArr->set_path($result, $keyPath, $value);
        }

        if (null !== $bind) {
            $this->applyBind($bind, $result);
        }

        return $result;
    }

    public function invalidAttributes(array $fillKeys = []) : array
    {
        if (! $this->isBuilt) {
            $this->build();

            $this->isBuilt = true;
        }

        if (! $this->isProcessed) {
            $this->process();

            $this->isProcessed = true;
        }

        $hasValue = ([] !== $fillKeys);

        $result = [];

        foreach ( $this->dataIndex as $keyNulpath => $value ) {
            $hasErrors = isset($this->errorsByKeyNulpath[ $keyNulpath ]);

            if (! $hasErrors) {
                continue;
            }

            if (is_array($value) && ([] !== $value)) {
                continue;
            }

            $keyPath = $this->dataPathes[ $keyNulpath ];

            $keyDotpath = implode('.', $keyPath);

            $result[ $keyDotpath ] = $hasValue
                ? $fillKeys[ 0 ]
                : $value;
        }

        return $result;
    }


    public function validated(&$bind = null) : array
    {
        if (! $this->isBuilt) {
            $this->build();

            $this->isBuilt = true;
        }

        if (! $this->isProcessed) {
            $this->process();

            $this->isProcessed = true;
        }

        $theArr = Lib::arr();

        $result = [];

        foreach ( $this->dataIndex as $keyNulpath => $value ) {
            $hasRules = isset($this->rulesByKeyNulpath[ $keyNulpath ]);

            if (! $hasRules) {
                continue;
            }

            if (is_array($value) && ([] !== $value)) {
                continue;
            }

            $keyPath = $this->dataPathes[ $keyNulpath ];

            $theArr->set_path($result, $keyPath, $value);
        }

        if (null !== $bind) {
            $this->applyBind($bind, $result);
        }

        return $result;
    }

    public function validatedAttributes(array $fillKeys = []) : array
    {
        if (! $this->isBuilt) {
            $this->build();

            $this->isBuilt = true;
        }

        if (! $this->isProcessed) {
            $this->process();

            $this->isProcessed = true;
        }

        $hasValue = ([] !== $fillKeys);

        $result = [];

        foreach ( $this->dataIndex as $keyNulpath => $value ) {
            $hasRules = isset($this->rulesByKeyNulpath[ $keyNulpath ]);

            if (! $hasRules) {
                continue;
            }

            if (is_array($value) && ([] !== $value)) {
                continue;
            }

            $keyPath = $this->dataPathes[ $keyNulpath ];

            $keyDotpath = implode('.', $keyPath);

            $result[ $keyDotpath ] = $hasValue
                ? $fillKeys[ 0 ]
                : $value;
        }

        return $result;
    }


    /**
     * @return static
     */
    public function modeApi(?bool $modeApi = null)
    {
        // > режим подразумевает, что запрос получен из API
        // > все NULL, оказавшиеся в данных, будут удалены, будто их не передавали

        $last = $this->modeApi;

        $this->modeApi = $modeApi ?? false;

        if ($last !== $this->modeApi) {
            $this->isBuilt = false;
            $this->isProcessed = false;
        }

        return $this;
    }

    /**
     * @return static
     */
    public function modeWeb(?bool $modeWeb = null)
    {
        // > режим подразумевает, что запрос получен из HTML-формы
        // > все пустые строки, оказавшиеся в данных, будут удалены, будто их не передавали

        $last = $this->modeWeb;

        $this->modeWeb = $modeWeb ?? false;

        if ($last !== $this->modeWeb) {
            $this->isBuilt = false;
            $this->isProcessed = false;
        }

        return $this;
    }


    /**
     * @return static
     */
    public function addData(array $data)
    {
        if ([] === $data) {
            return $this;
        }

        $this->pushData($data);

        return $this;
    }

    /**
     * @template T of string|RuleInterface|GenericRule
     * @template TT of callable|GenericFilter
     *
     * @param array<string, T|T[]>   $rules
     * @param array<string, TT|TT[]> $filters
     * @param array<string, mixed>   $defaults
     *
     * @return static
     */
    public function addRulesMap(
        array $rules = [],
        array $filters = [],
        array $defaults = []
    )
    {
        if ([] !== $rules) {
            $this->pushRules($rules);
        }

        if ([] !== $filters) {
            $this->pushFilters($filters);
        }

        if ([] !== $defaults) {
            $this->pushDefaults($defaults);
        }

        return $this;
    }

    /**
     * @param array<string|RuleInterface>|string|RuleInterface $rules
     * @param callable|GenericFilter                           $filter
     * @param array{ 0?: mixed }                               $default
     *
     * @return static
     */
    public function addRules(
        string $rulepathString,
        $rules, $filter = null, array $default = []
    )
    {
        if (null === $rules) {
            throw new LogicException(
                [
                    ''
                    . 'The `rules` should be string or instance one of: '
                    . '[ ' . implode(' ][ ', [ RuleInterface::class, GenericFilter::class ]) . ' ]',
                    //
                    $rules,
                ]
            );
        }

        $this->pushRules([ $rulepathString => $rules ]);

        if (null !== $filter) {
            $this->pushFilters([ $rulepathString => $filter ]);
        }

        if ([] !== $default) {
            $this->pushDefaults([ $rulepathString => $default[ 0 ] ]);
        }

        return $this;
    }

    /**
     * @param callable|GenericFilter        $filter
     * @param array<callable|GenericFilter> $filters
     *
     * @return static
     */
    public function addFilters(
        string $rulepathString,
        $filter, ...$filters
    )
    {
        if (null === $filter) {
            throw new LogicException(
                [
                    'The `filter` should be callable or instance of: ' . GenericFilter::class,
                    $filter,
                ]
            );
        }

        array_unshift($filters, $filter);

        $this->pushFilters([ $rulepathString => $filters ]);

        return $this;
    }

    /**
     * @return static
     */
    public function addDefault(string $rulepathString, array $default)
    {
        if ([] === $default) {
            throw new LogicException(
                [ 'The `default` should be array with zero key', $default ]
            );
        }

        $this->pushDefaults([ $rulepathString => $default[ 0 ] ]);

        return $this;
    }


    public function fieldpath($path, ...$pathes) : array
    {
        $fieldpathArray = Lib::arr()->arrpath_dot(
            static::SYMBOL_FIELDPATH_SEPARATOR,
            $path, ...$pathes
        );

        $this->validateFieldpathArray($fieldpathArray);

        return $fieldpathArray;
    }

    public function fieldpathOrAbsolute($path, $pathCurrent) : array
    {
        $keyPath = $this->fieldpath($path);
        $keyPathCurrent = $this->fieldpath($pathCurrent);

        $keyString = implode(static::SYMBOL_FIELDPATH_SEPARATOR, $keyPath);
        $keyStringCurrent = implode(static::SYMBOL_FIELDPATH_SEPARATOR, $keyPathCurrent);

        $keyStringAbsolute = Lib::php()->path_or_absolute(
            $keyString, $keyStringCurrent,
            static::SYMBOL_FIELDPATH_SEPARATOR, static::SYMBOL_FIELDPATH_PARENT
        );

        $keyStringAbsolute = ltrim(
            $keyStringAbsolute,
            static::SYMBOL_FIELDPATH_SEPARATOR
        );

        if ('' === $keyStringAbsolute) {
            throw new LogicException(
                'Result path should be non-empty string',
                $path,
                $pathCurrent
            );
        }

        $keyPath = explode(static::SYMBOL_FIELDPATH_SEPARATOR, $keyStringAbsolute);

        return $keyPath;
    }

    protected function validateFieldpathArray(array $dotRulepathArray) : void
    {
        $list = [
            static::SYMBOL_NUL                 => true,
            static::SYMBOL_FIELDPATH_SEPARATOR => true,
        ];

        $regex = '/[\x{0}\x{2F}]/iu';

        foreach ( $dotRulepathArray as $i => $p ) {
            if (preg_match($regex, $p)) {
                throw new LogicException(
                    [
                        ''
                        . 'The `key` should not contain symbols: '
                        . '[ ' . implode(' ][ ', array_keys($list)) . ' ]',
                        //
                        $p,
                        $i,
                    ]
                );
            }
        }
    }


    protected function build() : void
    {
        $this->filtersMerged = null;
        $this->defaultsMerged = null;
        $this->rulesMerged = null;

        $this->data = null;
        $this->dataMerged = null;

        $this->dataIndex = null;
        $this->dataPathes = null;
        $this->dataTypes = null;

        $this->dataMergedIndex = null;

        $this->buildFilters();
        $this->buildDefaults();
        $this->buildRules();

        $this->buildData();
        $this->buildDataIndex();
    }

    protected function buildFilters() : void
    {
        $filtersMerged = [];

        foreach ( $this->filtersQueue as $filtersQueueItem ) {
            foreach ( $filtersQueueItem as $rulepathWildcardString => $filtersList ) {
                $dotpathWildcardString = $this->nulpathFromDotpath($rulepathWildcardString);

                $filtersMerged[ $dotpathWildcardString ] = $filtersMerged[ $dotpathWildcardString ] ?? [];
                $filtersMerged[ $dotpathWildcardString ] = array_merge(
                    $filtersMerged[ $dotpathWildcardString ],
                    $filtersList
                );
            }
        }

        $this->filtersMerged = $filtersMerged;
    }

    protected function buildDefaults() : void
    {
        $defaultsMerged = [];

        foreach ( $this->defaultsQueue as $defaultsQueueItem ) {
            foreach ( $defaultsQueueItem as $rulepathWildcardString => $default ) {
                $dotpathWildcardString = $this->nulpathFromDotpath($rulepathWildcardString);

                if ([] === $default) {
                    unset($defaultsMerged[ $dotpathWildcardString ]);

                } else {
                    $defaultsMerged[ $dotpathWildcardString ] = $default[ 0 ];
                }
            }
        }

        $this->defaultsMerged = $defaultsMerged;
    }

    protected function buildRules() : void
    {
        $rulesMerged = [];

        foreach ( $this->rulesQueue as $rulesQueueItem ) {
            foreach ( $rulesQueueItem as $rulepathWildcardString => $rulesList ) {
                $wildcardDotpathString = $this->nulpathFromDotpath($rulepathWildcardString);

                $rulesMerged[ $wildcardDotpathString ] = $rulesMerged[ $wildcardDotpathString ] ?? [];
                $rulesMerged[ $wildcardDotpathString ] = array_merge(
                    $rulesMerged[ $wildcardDotpathString ],
                    $rulesList
                );
            }
        }

        $this->rulesMerged = $rulesMerged;
    }

    protected function buildData() : void
    {
        $data = [];

        foreach ( $this->dataQueue as $dataItem ) {
            $data = array_replace_recursive(
                $data,
                $dataItem
            );
        }

        $dataMerged = $data;

        $this->data = $data;
        $this->dataMerged = $dataMerged;
    }

    protected function buildDataIndex() : void
    {
        $theArr = Lib::arr();

        $data = $this->data;
        $dataMerged = $this->dataMerged;

        $dataMergedIndex = [];

        $gen = $theArr->walk_it(
            $dataMerged,
            _ARR_WALK_WITH_PARENTS | _ARR_WALK_WITH_EMPTY_ARRAYS
        );

        foreach ( $gen as $keyPath => &$value ) {
            $keyNulpath = ''
                . static::SYMBOL_NUL
                . implode(static::SYMBOL_NUL, $keyPath);

            $dataMergedIndex[ $keyNulpath ] =& $value;
        }

        $this->dataMergedIndex = $dataMergedIndex;

        $dataIndex = [];
        $dataPathes = [];
        $dataTypes = [];

        $gen = $theArr->walk_it(
            $data,
            _ARR_WALK_WITH_PARENTS | _ARR_WALK_WITH_EMPTY_ARRAYS
        );

        foreach ( $gen as $keyPath => &$value ) {
            $keyNulpath = ''
                . static::SYMBOL_NUL
                . implode(static::SYMBOL_NUL, $keyPath);

            $dataIndex[ $keyNulpath ] =& $value;
            $dataPathes[ $keyNulpath ] = $keyPath;
            $dataTypes[ $keyNulpath ] = static::VALUE_TYPE_ORIGINAL;
        }

        $this->dataIndex = $dataIndex;
        $this->dataPathes = $dataPathes;
        $this->dataTypes = $dataTypes;
    }


    protected function process() : void
    {
        if (! $this->isBuilt) {
            throw new RuntimeException(
                'The `->build()` should be called first'
            );
        }

        $this->rulesByKeyNulpath = null;
        $this->errorsByKeyNulpath = null;
        $this->messagesByKeyNulpath = null;

        $this->processData();
        $this->processFilters();
        $this->processDefaults();
        $this->processRules();

        $this->processValidation();
    }

    protected function processData() : void
    {
        $theArr = Lib::arr();

        $isModeApi = $this->modeApi;
        $isModeWeb = $this->modeWeb;

        $gen = $theArr->walk_it(
            $this->data,
            _ARR_WALK_WITH_PARENTS | _ARR_WALK_WITH_EMPTY_ARRAYS
        );

        foreach ( $gen as $keyPath => $value ) {
            $keyNulpath = ''
                . static::SYMBOL_NUL
                . implode(static::SYMBOL_NUL, $keyPath);

            if (false
                || ($isModeApi && (null === $value))
                || ($isModeWeb && ('' === $value))
            ) {
                unset($this->dataIndex[ $keyNulpath ]);
                unset($this->dataPathes[ $keyNulpath ]);
                unset($this->dataTypes[ $keyNulpath ]);

                $keyPathObject = ArrPath::fromValidArray($keyPath);

                $theArr->unset_path(
                    $this->data,
                    $keyPathObject
                );

            } else {
                $this->dataTypes[ $keyNulpath ] = static::VALUE_TYPE_FILTERED;
            }
        }
    }

    protected function processFilters() : void
    {
        $theArr = Lib::arr();

        $symbolSequence = static::SYMBOL_DOTPATH_WILDCARD_SEQUENCE;

        foreach ( $this->filtersMerged as $wildcardDotpath => $filters ) {
            $hasWildcard = (false !== strpos($wildcardDotpath, $symbolSequence));

            if ($hasWildcard) {
                $keyNulpathesOfFilters = $this->matchKeyNulpathesByWildcardDotpath($wildcardDotpath);

            } else {
                $keyNulpathesOfFilters = [ $wildcardDotpath ];
            }

            foreach ( $keyNulpathesOfFilters as $keyNulpath ) {
                $hasValue = $this->hasFilteredByIndex($keyNulpath, $value);

                if ($hasValue) {
                    $thePath = $this->dataPathes[ $keyNulpath ];
                    $thePathObject = ArrPath::fromValidArray($thePath);
                    $theValue = [ $value ];

                } else {
                    $thePath = explode(
                        static::SYMBOL_NUL,
                        ltrim($keyNulpath, static::SYMBOL_NUL)
                    );
                    $thePathObject = ArrPath::fromValidArray($thePath);
                    $theValue = [];
                }

                $theKey = end($thePath);

                $current = $theValue;
                foreach ( $filters as $filter ) {
                    $current = $this->processor->processFilter(
                        $filter,
                        $current, $theKey, $thePath,
                        $this
                    );
                }

                if ([] !== $current) {
                    $valueNew = $current[ 0 ];

                    $ref =& $theArr->put_path(
                        $this->data,
                        $thePathObject,
                        $valueNew
                    );

                    $this->dataIndex[ $keyNulpath ] =& $ref;
                    $this->dataPathes[ $keyNulpath ] = $thePath;
                    $this->dataTypes[ $keyNulpath ] = static::VALUE_TYPE_FILTERED;

                    if (is_array($valueNew)) {
                        $gen = $theArr->walk_it(
                            $valueNew,
                            _ARR_WALK_WITH_PARENTS | _ARR_WALK_WITH_EMPTY_ARRAYS
                        );

                        foreach ( $gen as $subkeyPath => $subkeyValue ) {
                            $subkeyPath = array_merge($thePath, $subkeyPath);
                            $subkeyNulpath = ''
                                . static::SYMBOL_NUL
                                . implode(static::SYMBOL_NUL, $subkeyPath);

                            $ref =& $theArr->fetch_path($this->data, $subkeyPath);

                            $this->dataIndex[ $subkeyNulpath ] =& $ref;
                            $this->dataPathes[ $subkeyNulpath ] = $subkeyPath;
                            $this->dataTypes[ $subkeyNulpath ] = static::VALUE_TYPE_FILTERED;
                        }
                    }

                } else {
                    $theArr->unset_path(
                        $this->data,
                        $thePathObject
                    );

                    unset($this->dataIndex[ $keyNulpath ]);
                    unset($this->dataPathes[ $keyNulpath ]);
                    unset($this->dataTypes[ $keyNulpath ]);
                }

                if ($hasWildcard) {
                    unset($this->cacheMatchKeyDotpathesByWildcardDotpath[ $wildcardDotpath ]);
                }
            }
        }
    }

    protected function processDefaults() : void
    {
        $theArr = Lib::arr();

        foreach ( $this->defaultsMerged as $wildcardDotpath => $valueDefault ) {
            $hasWildcard = strpos(
                $wildcardDotpath,
                static::SYMBOL_DOTPATH_WILDCARD_SEQUENCE
            );
            $hasWildcard = (false !== $hasWildcard);

            if ($hasWildcard) {
                $keyNulpathesOfDefaults = $this->matchKeyNulpathesByWildcardDotpath($wildcardDotpath);

            } else {
                $keyNulpathesOfDefaults = [ $wildcardDotpath ];
            }

            foreach ( $keyNulpathesOfDefaults as $keyNulpath ) {
                $hasValue = $this->hasDefaultByIndex($keyNulpath, $value);

                $isNoValue = false;
                $isValueEqualsDefault = false;

                if ($hasValue) {
                    $thePath = $this->dataPathes[ $keyNulpath ];
                    $thePathObject = ArrPath::fromValidArray($thePath);

                    $isValueEqualsDefault = ($value === $valueDefault);

                } else {
                    $thePath = explode(
                        static::SYMBOL_NUL,
                        ltrim($keyNulpath, static::SYMBOL_NUL)
                    );
                    $thePathObject = ArrPath::fromValidArray($thePath);

                    $isNoValue = true;
                }

                if ($isValueEqualsDefault) {
                    $this->dataTypes[ $keyNulpath ] = static::VALUE_TYPE_DEFAULT;

                } elseif ($isNoValue) {
                    $ref =& $theArr->put_path(
                        $this->data,
                        $thePathObject,
                        $valueDefault
                    );

                    $this->dataPathes[ $keyNulpath ] = $thePath;
                    $this->dataIndex[ $keyNulpath ] =& $ref;
                    $this->dataTypes[ $keyNulpath ] = static::VALUE_TYPE_DEFAULT;

                    if (is_array($valueDefault) && ([] !== $valueDefault)) {
                        $gen = $theArr->walk_it(
                            $valueDefault,
                            _ARR_WALK_WITH_PARENTS | _ARR_WALK_WITH_EMPTY_ARRAYS
                        );

                        foreach ( $gen as $subkeyPath => $subkeyValue ) {
                            $subkeyPath = array_merge($thePath, $subkeyPath);
                            $subkeyNulpath = ''
                                . static::SYMBOL_NUL
                                . implode(static::SYMBOL_NUL, $subkeyPath);

                            $ref =& $theArr->fetch_path($this->data, $subkeyPath);

                            $this->dataIndex[ $subkeyNulpath ] =& $ref;
                            $this->dataPathes[ $subkeyNulpath ] = $subkeyPath;
                            $this->dataTypes[ $subkeyNulpath ] = static::VALUE_TYPE_DEFAULT;
                        }
                    }
                }

                if ($hasWildcard) {
                    unset($this->cacheMatchKeyDotpathesByWildcardDotpath[ $wildcardDotpath ]);
                }
            }
        }
    }

    protected function processRules() : void
    {
        /**
         * @var array<string, GenericRule[]> $rulesByKeyNulpath
         */

        $rulesByKeyNulpath = [];

        foreach ( $this->rulesMerged as $wildcardDotpathString => $rules ) {
            $hasWildcard = strpos(
                $wildcardDotpathString,
                static::SYMBOL_DOTPATH_WILDCARD_SEQUENCE
            );
            $hasWildcard = (false !== $hasWildcard);

            if ($hasWildcard) {
                $keyNulpathesOfRules = $this->matchKeyNulpathesByWildcardDotpath($wildcardDotpathString);

            } else {
                $keyNulpathesOfRules = [ $wildcardDotpathString ];
            }

            foreach ( $keyNulpathesOfRules as $keyNulpath ) {
                $rulesByKeyNulpath[ $keyNulpath ] = $rulesByKeyNulpath[ $keyNulpath ] ?? [];
                $rulesByKeyNulpath[ $keyNulpath ] = array_merge(
                    $rulesByKeyNulpath[ $keyNulpath ],
                    $rules
                );
            }
        }

        foreach ( $rulesByKeyNulpath as $keyNulpath => $rules ) {
            $dataType = $this->dataTypes[ $keyNulpath ] ?? 0;

            if (static::VALUE_TYPE_DEFAULT === $dataType) {
                unset($rulesByKeyNulpath[ $keyNulpath ]);
            }
        }

        foreach ( $rulesByKeyNulpath as $keyNulpath => $rules ) {
            $hasValue = $this->hasFilteredByIndex($keyNulpath);
            $hasImplicitRule = false;

            $ruleClasses = [];
            $ruleInstances = [];
            foreach ( $rules as $i => $rule ) {
                $ruleInstances[ $i ] = null;

                $ruleObject = null;
                $ruleClass = null;
                if (! (false
                    || ($ruleObject = $rule->hasRuleInstance())
                    || ($ruleClass = $rule->hasRuleClass())
                )) {
                    throw new RuntimeException(
                        [
                            'Each of `rules` should be valid object of: ' . GenericRule::class,
                            $rule,
                            $i,
                        ]
                    );
                }

                if (false === $hasImplicitRule) {
                    $hasImplicitRule = is_subclass_of(
                        $ruleObject ?? $ruleClass,
                        RuleImplicitInterface::class
                    );
                }

                if ($ruleObject) {
                    $ruleInstances[ $i ] = $ruleObject;

                } else {
                    $ruleClasses[ $i ] = $ruleClass;
                }
            }

            // > если значения нет, и нет принудительных (Implicit) правил
            // > то ключ пропускается и проверки не выполняются
            // > из соображений скорости работы
            if (! ($hasImplicitRule || $hasValue)) {
                unset($rulesByKeyNulpath[ $keyNulpath ]);

                continue;
            }

            foreach ( $ruleClasses as $i => $ruleClass ) {
                $rule = $rules[ $i ];

                $ruleInstance = $this->factory->newRule($rule);

                $ruleInstances[ $i ] = $ruleInstance;
            }

            foreach ( $ruleInstances as $i => $ruleInstance ) {
                $rulesByKeyNulpath[ $keyNulpath ][ $i ] = $ruleInstances[ $i ];
            }
        }

        $this->rulesByKeyNulpath = $rulesByKeyNulpath;
    }

    protected function processValidation() : void
    {
        $errorsByKeyNulpath = [];

        foreach ( $this->rulesByKeyNulpath as $keyNulpath => $rules ) {
            $hasValue = $this->hasFilteredByIndex($keyNulpath, $value);

            if ($hasValue) {
                $thePath = $this->dataPathes[ $keyNulpath ];
                $theValue = [ $value ];

            } else {
                $thePath = explode(static::SYMBOL_NUL, $keyNulpath);
                $theValue = [];
            }

            $theKey = end($thePath);

            foreach ( $rules as $rule ) {
                $message = null;
                $throwable = null;

                try {
                    $message = $rule->validate(
                        $theValue, $theKey, $thePath,
                        $this
                    );
                }
                catch ( \Throwable $throwable ) {
                }

                $hasError = (null !== $message) || (null !== $throwable);

                if ($hasError) {
                    $ruleParameters = $rule->getParameters();

                    $error = [
                        'message'    => $message,
                        'throwable'  => $throwable,
                        //
                        'value'      => $theValue,
                        'key'        => $theKey,
                        'path'       => $thePath,
                        //
                        'rule'       => $rule,
                        'parameters' => $ruleParameters,
                    ];

                    $errorsByKeyNulpath[ $keyNulpath ][] = $error;

                    // > если предыдущее правило закончилось провалом
                    // > валидатор перестает выполнять проверку следующих правил
                    // > из соображений скорости работы
                    break;
                }
            }
        }

        $this->errorsByKeyNulpath = $errorsByKeyNulpath;
    }


    protected function pushData(array $data) : void
    {
        if ([] === $data) {
            return;
        }

        $this->dataQueue[ $this->queueId++ ] = $data;

        $this->isBuilt = false;
        $this->isProcessed = false;
    }

    protected function pushFilters(array $filters) : void
    {
        if ([] === $filters) {
            return;
        }

        $thePhp = Lib::php();

        $filtersQueueItem = [];
        foreach ( $filters as $wildcardDotpath => $filterOrFilters ) {
            $filtersList = $thePhp->to_list($filterOrFilters, [], 'is_callable');

            foreach ( $filtersList as $filter ) {
                $genericFilter = GenericFilter::from($filter);

                $filtersQueueItem[ static::SYMBOL_NUL . $wildcardDotpath ][] = $genericFilter;
            }
        }

        $this->filtersQueue[ $this->queueId++ ] = $filtersQueueItem;

        $this->isBuilt = false;
        $this->isProcessed = false;
    }

    protected function pushRules(array $rules) : void
    {
        if ([] === $rules) {
            return;
        }

        $thePhp = Lib::php();

        $rulesQueueItem = [];
        foreach ( $rules as $wildcardDotpath => $rulesList ) {
            $rulesList = $thePhp->to_list($rulesList);

            foreach ( $rulesList as $rulesListItem ) {
                if (is_string($rulesListItem)) {
                    $rulesArray = explode(
                        static::SYMBOL_RULELIST_SEPARATOR,
                        $rulesListItem
                    );

                } else {
                    $rulesArray = [ $rulesListItem ];
                }

                foreach ( $rulesArray as $i => $rule ) {
                    if (is_object($rule)) {
                        $_rule = GenericRule::fromObject($rule);

                    } elseif (is_string($rule)) {
                        $_rule = GenericRule::fromRuleString(
                            $rule,
                            [
                                'registry'  => $this->registry,
                                //
                                'separator' => static::SYMBOL_RULEARGS_SEPARATOR,
                                'delimiter' => static::SYMBOL_RULEARGS_DELIMITER,
                            ]
                        );

                    } else {
                        throw new RuntimeException(
                            [
                                'Unable to create generic rule',
                                $rule,
                                $i,
                                $wildcardDotpath,
                            ]
                        );
                    }

                    $rulesQueueItem[ static::SYMBOL_NUL . $wildcardDotpath ][] = $_rule;
                }
            }
        }

        $this->rulesQueue[ $this->queueId++ ] = $rulesQueueItem;

        $this->isBuilt = false;
        $this->isProcessed = false;
    }

    protected function pushDefaults(array $defaults) : void
    {
        if ([] === $defaults) {
            return;
        }

        $defaultsQueueItem = [];
        foreach ( $defaults as $wildcardDotpath => $default ) {
            $defaultsQueueItem[ static::SYMBOL_NUL . $wildcardDotpath ][] = $default;
        }

        $this->defaultsQueue[ $this->queueId++ ] = $defaultsQueueItem;

        $this->isBuilt = false;
        $this->isProcessed = false;
    }


    protected function dotpath($path, ...$pathes) : array
    {
        $theArr = Lib::arr();
        $theType = Lib::type();

        $rulepathArray = [];

        $gen = $theArr->arrpath_it($path, ...$pathes);

        $first = true;
        foreach ( $gen as $p ) {
            if ($theType->string($pString, $p)) {
                if ('' === $pString) {
                    $rulepathArray[] = $pString;

                } else {
                    if ($first) {
                        $pString = ltrim($pString, static::SYMBOL_NUL);
                    }

                    $rulepathArrayCurrent = explode(static::SYMBOL_DOTPATH_SEPARATOR, $pString);

                    $rulepathArray = array_merge(
                        $rulepathArray,
                        $rulepathArrayCurrent
                    );
                }
            }

            if ($first) {
                $first = false;
            }
        }

        return $rulepathArray;
    }

    protected function validateDotpathArray(array $wildcardPathArray) : void
    {
        $list = [
            static::SYMBOL_NUL => true,
        ];

        $regex = '/\x{0}/iu';

        foreach ( $wildcardPathArray as $i => $p ) {
            if (preg_match($regex, $p)) {
                throw new LogicException(
                    [
                        ''
                        . 'The `key` should not contain symbols: '
                        . '[ ' . implode(' ][ ', array_keys($list)) . ' ]',
                        //
                        $p,
                        $i,
                    ]
                );
            }
        }
    }


    protected function nulpathFromDotpath($path, ...$pathes) : string
    {
        $dotpathArray = $this->dotpath($path, ...$pathes);

        $this->validateDotpathArray($dotpathArray);

        $dotRulepathString = ''
            . static::SYMBOL_NUL
            . implode(static::SYMBOL_NUL, $dotpathArray);

        return $dotRulepathString;
    }


    protected function matchKeyNulpathesByWildcardDotpath(string $wildcardDotpath) : array
    {
        if (! isset($this->cacheMatchKeyDotpathesByWildcardDotpath[ $wildcardDotpath ])) {
            $isLastWildcard = (static::SYMBOL_DOTPATH_WILDCARD_SEQUENCE === substr($wildcardDotpath, -1));

            $keyNulpathes = array_keys($this->dataIndex);

            $keyNulpathesMatch = Lib::str()->str_match(
                $wildcardDotpath, $keyNulpathes,
                static::SYMBOL_DOTPATH_WILDCARD_SEQUENCE,
                static::SYMBOL_NUL
            );

            foreach ( $keyNulpathesMatch as $i => $keyNulpath ) {
                $isArray = is_array($this->dataIndex[ $keyNulpath ]);

                if ($isLastWildcard && $isArray) {
                    // > ключи, которые заканчиваются на звездочку, например, `users.*`
                    // > не применяются, если значение содержит потомков или является пустым родителем
                    // > иначе какой-нибудь фильтр заменит значения-списки

                    unset($keyNulpathesMatch[ $i ]);
                }
            }

            $keyNulpathesMatchIndex = array_fill_keys($keyNulpathesMatch, true);

            $this->cacheMatchKeyDotpathesByWildcardDotpath[ $wildcardDotpath ] = $keyNulpathesMatchIndex;
        }

        return array_keys($this->cacheMatchKeyDotpathesByWildcardDotpath[ $wildcardDotpath ]);
    }


    protected function applyBind(&$bind, array $data)
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

        if (null !== $boundArray) {
            foreach ( $data as $key => $value ) {
                $boundArray[ $key ] = $value;
            }

        } elseif (null !== $boundObject) {
            Lib::arr()->map_to_object($data, $boundObject);
        }

        return $bind;
    }
}
