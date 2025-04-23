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
     * > если задавать правила в строке, то несколько правил можно разделять символом `|`
     */
    const SYMBOL_RULE_SEPARATOR = '|';
    /**
     * > символ `:` используется для указания аргументов правил, например, `unique:true` - первый аргумент $isStrict = true
     */
    const SYMBOL_RULEARGS_SEPARATOR = ':';
    /**
     * > символ `;` используется для разделения нескольких параметров `in_array:1,2,3;true` - в массиве [1,2,3] и $isStrict = true
     */
    const SYMBOL_RULEARGS_DELIMITER = ';';

    /**
     * > правила для быстрого поиска хранятся в индексированном массиве, где путь разделен NUL-байтами
     */
    const SYMBOL_DOTPATH_SEPARATOR = "\0";

    /**
     * > вы можете задавать правила для вложенных ключей например `path.to.key`
     */
    const SYMBOL_RULEPATH_SEPARATOR = '.';
    /**
     * > некоторые правила зависят от других ключей, к ним можно обращаться через `../../path/to/key`
     */
    const SYMBOL_FIELDPATH_SEPARATOR = '/';
    const SYMBOL_FIELDPATH_PARENT    = '.';

    /**
     * > в регистрации правил на ключи можно использовать символ `*`, чтобы обработать все значения массива одинаково
     */
    const SYMBOL_WILDCARD_SEQUENCE = '*';


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
    protected $modeApi;
    /**
     * @var bool
     */
    protected $modeWeb;

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
     * @var array<int, array<string, callable>>
     */
    protected $filtersQueue = [];
    /**
     * @var array<int, array<string, string|RuleInterface>
     */
    protected $rulesQueue = [];
    /**
     * @var array<int, array<string, mixed>>
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
    protected $dataFiltered;
    /**
     * @var array<string, string[]>
     */
    protected $dataFilteredIndex;

    /**
     * @var array
     */
    protected $dataDefaults;
    /**
     * @var array<string, string[]>
     */
    protected $dataDefaultsIndex;

    /**
     * @var array
     */
    protected $dataValid;

    /**
     * @var array<string, array<string, bool>>
     */
    protected $cacheMatchDotKeypathesByDotRulepath;

    /**
     * @var array<string, RuleInterface[]>
     */
    protected $rulesByDotKeypath;
    /**
     * @var array<string, array[]>
     */
    protected $errorsByDotKeypath;
    /**
     * @var array<string, string[]>
     */
    protected $messagesByDotKeypath;


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

        $theArr = Lib::arr();

        $status = false
            || $theArr->has_path($this->dataFiltered, $path, [ &$val ])
            || $theArr->has_path($this->dataDefaults, $path, [ &$val ]);

        if ($status) {
            $value = $val;
        }

        return $status;
    }

    public function get($path, array $fallback = []) // : mixed
    {
        $theArr = Lib::arr();

        $value = null
            ?? $theArr->get_path($this->dataFiltered, $path, [ null ])
            ?? $theArr->get_path($this->dataDefaults, $path, [ null ])
            ?? $this;

        if ($this === $value) {
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


    public function hasOriginal($path, &$value = null) : bool
    {
        $value = null;

        $status = Lib::arr()->has_path($this->dataMerged, $path, [ &$val ]);

        if ($status) {
            $value = $val;
        }

        return $status;
    }

    public function getOriginal($path, array $fallback = []) // : mixed
    {
        $value = Lib::arr()->get_path($this->dataMerged, $path, [ $this ]);

        if ($this === $value) {
            if ($fallback) {
                [ $fallback ] = $fallback;

                return $fallback;
            }

            throw new RuntimeException(
                [
                    'Missing path in original data',
                    $path,
                ]
            );
        }

        return $value;
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

        if (null !== $bind) {
            $dotArray = Lib::arr()->dot($this->dataValid);

            $this->applyBind($bind, $dotArray);
        }

        return $this->dataValid;
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

        if (null !== $bind) {
            $dotArray = Lib::arr()->dot($this->dataValid);

            $this->applyBind($bind, $dotArray);
        }

        return $this->dataValid;
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

        $status = ([] === $this->errorsByDotKeypath);

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
        foreach ( $this->errorsByDotKeypath as $dotpath => $array ) {
            $key = implode('.',
                explode(static::SYMBOL_DOTPATH_SEPARATOR, $dotpath)
            );

            $errors[ $key ] = $array;
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

        $messages = [];
        foreach ( $this->messagesByDotKeypath as $dotpath => $array ) {
            $key = implode('.',
                explode(static::SYMBOL_DOTPATH_SEPARATOR, $dotpath)
            );

            $messages[ $key ] = $array;
        }

        return $messages;
    }


    public function getRules() : array
    {
        if (! $this->isBuilt) {
            $this->build();

            $this->isBuilt = true;
        }

        $result = [];

        foreach ( $this->rulesMerged as $dotKeypath => $list ) {
            $thePath = explode(static::SYMBOL_DOTPATH_SEPARATOR, $dotKeypath);

            $dotPath = implode('.', $thePath);

            $result[ $dotPath ] = $list;
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
            foreach ( array_keys($this->rulesMerged) as $dotKeypath ) {
                $thePath = explode(static::SYMBOL_DOTPATH_SEPARATOR, $dotKeypath);

                $dotPath = implode('.', $thePath);

                $result[ $dotPath ] = $fillKeys[ 0 ];
            }

        } else {
            foreach ( $this->rulesMerged as $dotKeypath => $list ) {
                $thePath = explode(static::SYMBOL_DOTPATH_SEPARATOR, $dotKeypath);

                $dotPath = implode('.', $thePath);

                $list = array_map('strval', $list);
                $list = implode('|', $list);

                $result[ $dotPath ] = $list;
            }
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

        $result = $this->dataValid;

        if ([] !== $this->errorsByDotKeypath) {
            $theArr = Lib::arr();

            foreach ( array_keys($this->errorsByDotKeypath) as $dotKeypath ) {
                $thePath = explode(static::SYMBOL_DOTPATH_SEPARATOR, $dotKeypath);
                $thePathObject = ArrPath::fromValid($thePath);

                if ($theArr->has_path($this->dataMerged, $thePathObject, [ &$theValue ])) {
                    $theArr->set_path($result, $thePathObject, $theValue);
                }
            }
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

        $hasValue = ([] !== $fillKeys);

        $theArr = Lib::arr();

        $result = $theArr->dot(
            $this->dataValid, '.',
            _ARR_WALK_WITH_EMPTY_ARRAYS | _ARR_WALK_WITH_LISTS
        );

        foreach ( array_keys($this->errorsByDotKeypath) as $dotKeypath ) {
            $thePath = explode(static::SYMBOL_DOTPATH_SEPARATOR, $dotKeypath);

            $dotPath = implode('.', $thePath);

            if ($hasValue) {
                $result[ $dotPath ] = $fillKeys[ 0 ];

            } else {
                $thePathObject = ArrPath::fromValid($thePath);

                $theArr->has_path($this->dataMerged, $thePathObject, [ &$theValue ]);

                $result[ $dotPath ] = $theValue;
            }
        }

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

        $result = $this->dataValid;

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

        $result = Lib::arr()->dot(
            $this->dataValid, '.',
            _ARR_WALK_WITH_EMPTY_ARRAYS | _ARR_WALK_WITH_LISTS
        );

        if ($hasValue) {
            $result = array_fill_keys(
                array_keys($result),
                $fillKeys[ 0 ]
            );
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

        $result = [];

        $theArr = Lib::arr();

        foreach ( array_keys($this->errorsByDotKeypath) as $dotKeypath ) {
            $thePath = explode(static::SYMBOL_DOTPATH_SEPARATOR, $dotKeypath);
            $thePathObject = ArrPath::fromValid($thePath);

            if ($theArr->has_path($this->dataMerged, $thePathObject, [ &$theValue ])) {
                $theArr->set_path($result, $thePathObject, $theValue);
            }
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

        $theArr = Lib::arr();

        foreach ( array_keys($this->errorsByDotKeypath) as $dotKeypath ) {
            $thePath = explode(static::SYMBOL_DOTPATH_SEPARATOR, $dotKeypath);

            $dotPath = implode('.', $thePath);

            if ($hasValue) {
                $result[ $dotPath ] = $fillKeys[ 0 ];

            } else {
                $thePathObject = ArrPath::fromValid($thePath);

                $theArr->has_path($this->dataMerged, $thePathObject, [ &$theValue ]);

                $result[ $dotPath ] = $theValue;
            }
        }

        return $result;
    }


    public function touched(&$bind = null) : array
    {
        if (! $this->isBuilt) {
            $this->build();

            $this->isBuilt = true;
        }

        if (! $this->isProcessed) {
            $this->process();

            $this->isProcessed = true;
        }

        $result = [];

        $theArr = Lib::arr();

        foreach ( array_keys($this->rulesByDotKeypath) as $dotKeypath ) {
            $thePath = explode(static::SYMBOL_DOTPATH_SEPARATOR, $dotKeypath);
            $thePathObject = ArrPath::fromValid($thePath);

            if ($theArr->has_path($this->dataMerged, $thePathObject, [ &$theValue ])) {
                $theArr->set_path($result, $thePathObject, $theValue);
            }
        }

        if (null !== $bind) {
            $this->applyBind($bind, $result);
        }

        return $result;
    }

    public function touchedAttributes(array $fillKeys = []) : array
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

        $theArr = Lib::arr();

        foreach ( array_keys($this->rulesByDotKeypath) as $dotKeypath ) {
            $thePath = explode(static::SYMBOL_DOTPATH_SEPARATOR, $dotKeypath);

            $dotPath = implode('.', $thePath);

            if ($hasValue) {
                $result[ $dotPath ] = $fillKeys[ 0 ];

            } else {
                $thePathObject = ArrPath::fromValid($thePath);

                $theArr->has_path($this->dataMerged, $thePathObject, [ &$theValue ]);

                $result[ $dotPath ] = $theValue;
            }
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

        $result = [];

        $theArr = Lib::arr();

        foreach ( array_keys($this->rulesByDotKeypath) as $dotKeypath ) {
            if (isset($this->errorsByDotKeypath[ $dotKeypath ])) {
                continue;
            }

            $thePath = explode(static::SYMBOL_DOTPATH_SEPARATOR, $dotKeypath);
            $thePathObject = ArrPath::fromValid($thePath);

            if ($theArr->has_path($this->dataFiltered, $thePathObject, [ &$theValue ])) {
                $theArr->set_path($result, $thePathObject, $theValue);
            }
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

        $theArr = Lib::arr();

        foreach ( array_keys($this->rulesByDotKeypath) as $dotKeypath ) {
            if (isset($this->errorsByDotKeypath[ $dotKeypath ])) {
                continue;
            }

            $thePath = explode(static::SYMBOL_DOTPATH_SEPARATOR, $dotKeypath);

            $dotPath = implode('.', $thePath);

            if ($hasValue) {
                $result[ $dotPath ] = $fillKeys[ 0 ];

            } else {
                $thePathObject = ArrPath::fromValid($thePath);

                $theArr->has_path($this->dataFiltered, $thePathObject, [ &$theValue ]);

                $result[ $dotPath ] = $theValue;
            }
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

        $modeApi = $modeApi ?? true;

        $last = $this->modeApi;

        if ($last !== $modeApi) {
            $this->isBuilt = false;
            $this->isProcessed = false;
        }

        $this->modeApi = $modeApi;

        return $this;
    }

    /**
     * @return static
     */
    public function modeWeb(?bool $modeWeb = null)
    {
        // > режим подразумевает, что запрос получен из HTML-формы
        // > все пустые строки, оказавшиеся в данных, будут удалены, будто их не передавали

        $modeWeb = $modeWeb ?? true;

        $last = $this->modeWeb;

        if ($last !== $modeWeb) {
            $this->isBuilt = false;
            $this->isProcessed = false;
        }

        $this->modeWeb = $modeWeb;

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
        if ([] !== $filters) {
            $this->pushFilters($filters);
        }

        if ([] !== $rules) {
            $this->pushRules($rules);
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


    public function rulePath($path, ...$pathes) : array
    {
        return Lib::arr()->arrpath_dot(
            static::SYMBOL_RULEPATH_SEPARATOR,
            $path, ...$pathes
        );
    }


    public function fieldPath($path, ...$pathes) : array
    {
        return Lib::arr()->arrpath_dot(
            static::SYMBOL_FIELDPATH_SEPARATOR,
            $path, ...$pathes
        );
    }

    public function fieldPathOrAbsolute($path, $pathCurrent) : array
    {
        $thePhp = Lib::php();

        $keyPath = $this->fieldPath($path);
        $keyPathCurrent = $this->fieldPath($pathCurrent);

        $keyString = implode(static::SYMBOL_FIELDPATH_SEPARATOR, $keyPath);
        $keyStringCurrent = implode(static::SYMBOL_FIELDPATH_SEPARATOR, $keyPathCurrent);

        $keyStringAbsolute = $thePhp->path_or_absolute(
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


    protected function build() : void
    {
        $this->dataMerged = [];
        $this->filtersMerged = [];
        $this->defaultsMerged = [];
        $this->rulesMerged = [];

        $this->buildData();
        $this->buildFilters();
        $this->buildDefaults();
        $this->buildRules();
    }

    protected function buildData() : void
    {
        $dataMerged = [];

        foreach ( $this->dataQueue as $dataItem ) {
            $dataMerged = array_replace(
                $dataMerged,
                $dataItem
            );
        }

        $this->dataMerged = $dataMerged;
    }

    protected function buildFilters() : void
    {
        $filtersMerged = [];

        foreach ( $this->filtersQueue as $filtersQueueItem ) {
            foreach ( $filtersQueueItem as $rulepathString => $filtersList ) {
                $dotRulepathString = $this->dotRulepathString($rulepathString);

                $filtersMerged[ $dotRulepathString ] = array_merge(
                    $filtersMerged[ $rulepathString ] ?? [],
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
            foreach ( $defaultsQueueItem as $rulepathString => $default ) {
                $dotRulepathString = $this->dotRulepathString($rulepathString);

                $defaultsMerged[ $dotRulepathString ] = $default;
            }
        }

        $this->defaultsMerged = $defaultsMerged;
    }

    protected function buildRules() : void
    {
        $rulesMerged = [];

        foreach ( $this->rulesQueue as $rulesQueueItem ) {
            foreach ( $rulesQueueItem as $rulepathString => $rulesList ) {
                $dotRulepathString = $this->dotRulepathString($rulepathString);

                $rulesMerged[ $dotRulepathString ] = array_merge(
                    $rulesMerged[ $rulepathString ] ?? [],
                    $rulesList
                );
            }
        }

        $this->rulesMerged = $rulesMerged;
    }


    protected function process() : void
    {
        if (! $this->isBuilt) {
            throw new RuntimeException(
                'The `->build()` should be called first'
            );
        }

        $this->dataFiltered = [];
        $this->dataFilteredIndex = [];
        $this->dataDefaults = [];
        $this->dataDefaultsIndex = [];
        $this->rulesByDotKeypath = [];
        $this->errorsByDotKeypath = [];
        $this->messagesByDotKeypath = [];
        $this->dataValid = [];

        $this->processDataFiltered();
        $this->processFilters();
        $this->processDefaults();
        $this->processRules();
        $this->processValidation();
        $this->processDataValid();
    }

    protected function processDataFiltered() : void
    {
        $theArr = Lib::arr();

        $gen = $theArr->walk_it(
            $this->dataMerged,
            _ARR_WALK_WITH_PARENTS | _ARR_WALK_WITH_EMPTY_ARRAYS
        );

        $dataFiltered = $this->dataMerged;
        $dataFilteredIndex = [];
        foreach ( $gen as $keypathArray => $value ) {
            if (false
                || ($this->modeApi && (null === $value))
                || ($this->modeWeb && ('' === $value))
            ) {
                $keypathObject = ArrPath::fromValid($keypathArray);

                $theArr->unset_path(
                    $dataFiltered,
                    $keypathObject
                );

                continue;
            }

            $dotKeypathString = implode(static::SYMBOL_DOTPATH_SEPARATOR, $keypathArray);

            $dataFilteredIndex[ $dotKeypathString ] = $keypathArray;
        }

        $this->dataFiltered = $dataFiltered;
        $this->dataFilteredIndex = $dataFilteredIndex;
    }

    protected function processFilters() : void
    {
        $theArr = Lib::arr();

        $symbolSequence = static::SYMBOL_WILDCARD_SEQUENCE;

        foreach ( $this->filtersMerged as $dotRulepathString => $filters ) {
            $hasWildcard = (false !== strpos($dotRulepathString, $symbolSequence));

            $isLastWildcard = false;
            if ($hasWildcard) {
                $isLastWildcard = ($symbolSequence === substr($dotRulepathString, -1));

                $dotKeypathesOfFilters = $this->matchDotKeypathesByDotRulepath($dotRulepathString);

            } else {
                $dotKeypathesOfFilters = [ $dotRulepathString ];
            }

            foreach ( $dotKeypathesOfFilters as $dotKeypathString ) {
                $hasValue = isset($this->dataFilteredIndex[ $dotKeypathString ]);

                $isValueArray = false;
                if ($hasValue) {
                    $thePath = $this->dataFilteredIndex[ $dotKeypathString ];
                    $thePathObject = ArrPath::fromValid($thePath);

                    $theValue = $theArr->get_path($this->dataFiltered, $thePathObject);

                    if (is_array($theValue)) {
                        $isValueArray = true;
                    }

                    $theValue = [ $theValue ];

                } else {
                    $thePath = explode(static::SYMBOL_DOTPATH_SEPARATOR, $dotKeypathString);
                    $thePathObject = ArrPath::fromValid($thePath);

                    $theValue = [];
                }

                if ($isLastWildcard && $isValueArray) {
                    // > фильтры, которые заканчиваются на звездочку, например, `users.*`
                    // > не применяются, если значение содержит потомков или является пустым родителем
                    // > иначе какой-нибудь `intval` фильтр посечёт значения-списки
                    continue;
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
                    $theArr->set_path(
                        $this->dataFiltered,
                        $thePathObject,
                        $current[ 0 ]
                    );

                } else {
                    $theArr->unset_path(
                        $this->dataFiltered,
                        $thePathObject
                    );

                    unset($this->dataFilteredIndex[ $dotKeypathString ]);

                    unset($this->cacheMatchDotKeypathesByDotRulepath[ $dotRulepathString ][ $dotKeypathString ]);
                }
            }
        }
    }

    protected function processDefaults() : void
    {
        $theArr = Lib::arr();

        $dataDefaults = [];
        $dataDefaultsIndex = [];

        foreach ( $this->defaultsMerged as $dotRulepathString => $theValueDefault ) {
            $hasWildcard = strpos(
                $dotRulepathString,
                static::SYMBOL_WILDCARD_SEQUENCE
            );
            $hasWildcard = (false !== $hasWildcard);

            if ($hasWildcard) {
                $dotKeypathesOfDefaults = $this->matchDotKeypathesByDotRulepath($dotRulepathString);

            } else {
                $dotKeypathesOfDefaults = [ $dotRulepathString ];
            }

            foreach ( $dotKeypathesOfDefaults as $dotKeypathString ) {
                $hasValue = isset($this->dataFilteredIndex[ $dotKeypathString ]);

                $isNoValue = false;
                $isValueEqualsDefault = false;

                if ($hasValue) {
                    $thePath = $this->dataFilteredIndex[ $dotKeypathString ];
                    $thePathObject = ArrPath::fromValid($thePath);

                    $theValue = $theArr->get_path($this->dataFiltered, $thePathObject);

                    $isValueEqualsDefault = ($theValueDefault === $theValue);

                } else {
                    $thePath = explode(static::SYMBOL_DOTPATH_SEPARATOR, $dotKeypathString);
                    $thePathObject = ArrPath::fromValid($thePath);

                    $isNoValue = true;
                }

                if (false
                    || $isNoValue
                    || $isValueEqualsDefault
                ) {
                    if ($isValueEqualsDefault) {
                        $theArr->unset_path(
                            $this->dataFiltered,
                            $thePathObject
                        );

                        $dataDefaultsIndex[ $dotKeypathString ] = $thePath;

                        unset($this->dataFilteredIndex[ $dotKeypathString ]);

                        unset($this->cacheMatchDotKeypathesByDotRulepath[ $dotRulepathString ][ $dotKeypathString ]);
                    }

                    $theArr->set_path(
                        $dataDefaults,
                        $thePathObject,
                        $theValueDefault
                    );
                }
            }
        }

        $this->dataDefaults = $dataDefaults;
        $this->dataDefaultsIndex = $dataDefaultsIndex;
    }

    protected function processRules() : void
    {
        /**
         * @var array<string, GenericRule[]> $rulesByDotKeypath
         */

        $rulesByDotKeypath = [];

        foreach ( $this->rulesMerged as $dotRulepathString => $rules ) {
            $hasWildcard = strpos(
                $dotRulepathString,
                static::SYMBOL_WILDCARD_SEQUENCE
            );
            $hasWildcard = (false !== $hasWildcard);

            if ($hasWildcard) {
                $dotKeypathesOfRules = $this->matchDotKeypathesByDotRulepath($dotRulepathString);

            } else {
                $dotKeypathesOfRules = [ $dotRulepathString ];
            }

            foreach ( $dotKeypathesOfRules as $dotKeypathString ) {
                $rulesByDotKeypath[ $dotKeypathString ] = $rulesByDotKeypath[ $dotKeypathString ] ?? [];
                $rulesByDotKeypath[ $dotKeypathString ] = array_merge(
                    $rulesByDotKeypath[ $dotKeypathString ],
                    $rules
                );
            }
        }

        foreach ( $rulesByDotKeypath as $dotKeypathString => $rules ) {
            $hasValue = isset($this->dataFilteredIndex[ $dotKeypathString ]);

            $ruleInstances = [];
            $ruleClasses = [];

            $hasImplicitRule = false;

            foreach ( $rules as $i => $rule ) {
                $ruleInstances[ $i ] = null;

                $ruleObject = null;
                $ruleClass = null;
                if (! (false
                    || ($ruleObject = $rule->hasInstance())
                    || ($ruleClass = $rule->hasClass())
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
                unset($rulesByDotKeypath[ $dotKeypathString ]);

                continue;
            }

            foreach ( $ruleClasses as $i => $ruleClass ) {
                $rule = $rules[ $i ];

                $ruleInstance = $this->factory->newRule($rule);

                $ruleInstances[ $i ] = $ruleInstance;
            }

            foreach ( $ruleInstances as $i => $ruleInstance ) {
                $rulesByDotKeypath[ $dotKeypathString ][ $i ] = $ruleInstances[ $i ];
            }
        }

        $this->rulesByDotKeypath = $rulesByDotKeypath;
    }

    protected function processValidation() : void
    {
        /**
         * @var array<string, array[]>  $errorsByDotKeypath
         * @var array<string, string[]> $messagesByDotKeypath
         */

        $theArr = Lib::arr();

        $errorsByDotKeypath = [];
        $messagesByDotKeypath = [];

        foreach ( $this->rulesByDotKeypath as $dotKeypath => $rules ) {
            $hasValue = isset($this->dataFilteredIndex[ $dotKeypath ]);

            if ($hasValue) {
                $thePath = $this->dataFilteredIndex[ $dotKeypath ];
                $thePathObject = ArrPath::fromValid($thePath);

                $theValue = $theArr->get_path($this->dataFiltered, $thePathObject);
                $theValue = [ $theValue ];

            } else {
                $thePath = explode(static::SYMBOL_DOTPATH_SEPARATOR, $dotKeypath);

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

                    $message = $this->translator->translate(
                        $message, $throwable,
                        $theValue, $theKey, $thePath,
                        $rule, $ruleParameters
                    );

                    $errorsByDotKeypath[ $dotKeypath ][] = $error;
                    $messagesByDotKeypath[ $dotKeypath ][] = $message;

                    // > если предыдущее правило закончилось провалом
                    // > валидатор перестает выполнять проверку следующих правил
                    // > из соображений скорости работы
                    break;
                }
            }
        }

        $this->errorsByDotKeypath = $errorsByDotKeypath;
        $this->messagesByDotKeypath = $messagesByDotKeypath;
    }

    protected function processDataValid() : void
    {
        $theArr = Lib::arr();

        $dataValid = $this->dataFiltered;

        if ([] !== $this->dataDefaults) {
            $gen = $theArr->walk_it($this->dataDefaults, _ARR_WALK_WITH_EMPTY_ARRAYS);

            foreach ( $gen as $keypathArray => $value ) {
                $keypathObject = ArrPath::fromValid($keypathArray);

                $theArr->set_path($dataValid, $keypathObject, $value);
            }
        }

        foreach ( array_keys($this->errorsByDotKeypath) as $dotKeypath ) {
            $keypathArray = explode(static::SYMBOL_DOTPATH_SEPARATOR, $dotKeypath);

            if ($theArr->has_path($dataValid, $keypathArray)) {
                $theArr->unset_path($dataValid, $keypathArray);

                array_pop($keypathArray);

                while ( [] !== $keypathArray ) {
                    if ([] === $theArr->get_path($dataValid, $keypathArray)) {
                        $theArr->unset_path($dataValid, $keypathArray);

                    } else {
                        break;
                    }

                    array_pop($keypathArray);
                }
            }
        }

        $this->dataValid = $dataValid;
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
        [ , $filtersDict ] = Lib::arr()->kwargs($filters);

        if ([] === $filtersDict) {
            return;
        }

        $thePhp = Lib::php();

        $filtersQueueItem = [];
        foreach ( $filtersDict as $keyWildcard => $filterOrFilters ) {
            $filtersList = $thePhp->to_list($filterOrFilters, [], 'is_callable');

            foreach ( $filtersList as $filter ) {
                $filtersQueueItem[ $keyWildcard ][] = GenericFilter::from($filter);
            }
        }

        $this->filtersQueue[ $this->queueId++ ] = $filtersQueueItem;

        $this->isBuilt = false;
        $this->isProcessed = false;
    }

    protected function pushRules(array $rules) : void
    {
        [ , $rulesDict ] = Lib::arr()->kwargs($rules);

        if ([] === $rulesDict) {
            return;
        }

        $thePhp = Lib::php();

        $rulesQueueItem = [];
        foreach ( $rulesDict as $keyWildcard => $ruleOrRules ) {
            $rulesList = $thePhp->to_list($ruleOrRules);

            foreach ( $rulesList as $ruleListItem ) {
                if (is_string($ruleListItem)) {
                    $rulesArray = explode(
                        static::SYMBOL_RULE_SEPARATOR,
                        $ruleListItem
                    );

                } else {
                    $rulesArray = [ $ruleListItem ];
                }

                foreach ( $rulesArray as $i => $rule ) {
                    if (is_object($rule)) {
                        $_rule = GenericRule::fromObject($rule);

                    } elseif (is_string($rule)) {
                        $_rule = GenericRule::fromString(
                            $rule,
                            [
                                'registry'  => $this->registry,
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
                                $keyWildcard,
                            ]
                        );
                    }

                    $rulesQueueItem[ $keyWildcard ][] = $_rule;
                }
            }
        }

        $this->rulesQueue[ $this->queueId++ ] = $rulesQueueItem;

        $this->isBuilt = false;
        $this->isProcessed = false;
    }

    protected function pushDefaults(array $defaults) : void
    {
        [ , $defaultsDict ] = Lib::arr()->kwargs($defaults);

        if ([] === $defaultsDict) {
            return;
        }

        $this->defaultsQueue[ $this->queueId++ ] = $defaultsDict;

        $this->isBuilt = false;
        $this->isProcessed = false;
    }


    protected function dotRulepath($rulepath, ...$rulepathes) : array
    {
        $dotpath = $this->rulePath($rulepath, ...$rulepathes);

        return $dotpath;
    }

    protected function dotRulepathString($rulepath, ...$rulepathes) : string
    {
        $dotRulepath = $this->dotRulepath($rulepath, ...$rulepathes);

        $this->validateDotRulepath($dotRulepath);

        $dotRulepathString = implode(static::SYMBOL_DOTPATH_SEPARATOR, $dotRulepath);

        return $dotRulepathString;
    }

    protected function validateDotRulepath(array $dotRulepath) : void
    {
        /**
         * @noinspection PhpDuplicateArrayKeysInspection
         */
        $list = [
            static::SYMBOL_DOTPATH_SEPARATOR   => true,
            //
            static::SYMBOL_RULE_SEPARATOR      => true,
            //
            static::SYMBOL_RULEARGS_SEPARATOR  => true,
            static::SYMBOL_RULEARGS_DELIMITER  => true,
            //
            static::SYMBOL_RULEPATH_SEPARATOR  => true,
            //
            static::SYMBOL_FIELDPATH_SEPARATOR => true,
            static::SYMBOL_FIELDPATH_PARENT    => true,
        ];

        $theStr = Lib::str();

        $fnStrlen = $theStr->mb_func('strlen');
        $fnSubstr = $theStr->mb_func('substr');

        foreach ( $dotRulepath as $i => $p ) {
            $len = $fnStrlen($p);

            for ( $ii = 0; $ii < $len; $ii++ ) {
                $letter = $fnSubstr($p, $ii, 1);

                if (isset($list[ $letter ])) {
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
    }


    protected function matchDotKeypathesByDotRulepath(string $dotRulepathString) : array
    {
        if (! isset($this->cacheMatchDotKeypathesByDotRulepath[ $dotRulepathString ])) {
            $dotKeypathes = array_keys($this->dataFilteredIndex);

            $dotKeypathesMatch = Lib::str()->str_match(
                $dotRulepathString, $dotKeypathes,
                static::SYMBOL_WILDCARD_SEQUENCE,
                static::SYMBOL_DOTPATH_SEPARATOR
            );

            $dotKeypathesMatchIndex = array_fill_keys($dotKeypathesMatch, true);

            $this->cacheMatchDotKeypathesByDotRulepath[ $dotRulepathString ] = $dotKeypathesMatchIndex;
        }

        return array_keys($this->cacheMatchDotKeypathesByDotRulepath[ $dotRulepathString ]);
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
