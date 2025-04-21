# Validator

Обертка `illuminate\validation` (валидатор произвольных массивов)

- с поддержкой всех функций laravel валидатора (лучше прочитать документацию)
- с отключенным приведением пустых строк к NULL, но возможностью удалить все пустые строки из данных через ->modeWeb()
- с возможностью удалить все NULL из данных через ->modeApi(), чтобы сделать поддержку например `{N}`, если в API интерпретировать NULL как "не трогать", а `{N}` как "очистить"
- с отключенным по-умолчанию механизмом перевода на другой язык для экономии ресурсов
- с механизмом фильтрации полей, применяемым после валидации
- с возможностью получить ->valid(&$ref)/validated(&$ref)/invalid(&$ref) поля или ->messages()
- с двумя функциями запуска ->validate(&$ref)/->inspect(&$ref), работают идентично, но рассчитаны на вызов в контроллере и в сервисном слое - и выбрасывающие 2 разных исключения, т.к. пользователю не нужно знать о внутренних валидациях
- с упрощенным добавлением своих правил через RuleInterface
- с несколькими дополнительными правилами вроде `dict`, `list`, `index`, `is_a`, `is_subclass_of` и другие, нужные при валидации CLI приложения, а не только HTML/WEB
- с возможностью соединять несколько валидаций в одну
- с возможностью запускать единожды созданный валидатор несколько раз, добавляя в него правила, данные и так далее, через паттерн Builder

## Установка

```
composer require gzhegow/validator;
```

## Пример и тесты

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';


// > настраиваем PHP
ini_set('memory_limit', '32M');


// > настраиваем обработку ошибок
(new \Gzhegow\Lib\Exception\ErrorHandler())
    ->useErrorReporting()
    ->useErrorHandler()
    ->useExceptionHandler()
;


// > добавляем несколько функция для тестирования
function _values($separator = null, ...$values) : string
{
    return \Gzhegow\Lib\Lib::debug()->values($separator, [], ...$values);
}

function _array_multiline($value, int $maxLevel = null, array $options = []) : string
{
    return \Gzhegow\Lib\Lib::debug()->value_array_multiline($value, $maxLevel, $options);
}

function _print(...$values) : void
{
    echo _values(' | ', ...$values) . PHP_EOL;
}

function _print_array_multiline($value, int $maxLevel = null, array $options = [])
{
    echo _array_multiline($value, $maxLevel, $options) . PHP_EOL;
}

function _assert_stdout(
    \Closure $fn, array $fnArgs = [],
    string $expectedStdout = null
) : void
{
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);

    \Gzhegow\Lib\Lib::test()->assertStdout(
        $trace,
        $fn, $fnArgs,
        $expectedStdout
    );
}


// > сначала всегда фабрика (она также создает Rule и Replacer, т.е. можно подключить IoC контейнер)
$factory = new \Gzhegow\Validator\ValidatorFactory();

// > создаем процессор для запуска правил/реплейсеров (сюда можно подключить IoC контейнер)
$processor = new \Gzhegow\Validator\ValidatorProcessor();

// > создаем фабрику валидатора laravel, её можно создать из шаблона (как в моей фабрике, или вручную, изучив шаблон)
$illuminateValidatorFactory = $factory->newIlluminateValidatorFactory(
    $processor
);

// > создаем фасад
$validator = new \Gzhegow\Validator\ValidatorFacade(
    $factory,
    $processor,
    //
    $illuminateValidatorFactory
);

// > регистрируем фасад в статику для удобного доступа из закрытых контекстов
\Gzhegow\Validator\Validator::setFacade($validator);


// > расширяем фасад, добавляя собственные правила под приложение
//
// > этих правил не хватало в ларавельной валидации, поскольку она работала в основном с HTML формами
$validator->extend('dict', \Gzhegow\Validator\Rule\DictRule::class);
$validator->extend('gettype', \Gzhegow\Validator\Rule\GettypeRule::class);
$validator->extend('index', \Gzhegow\Validator\Rule\IndexRule::class);
$validator->extend('instance_is_a', \Gzhegow\Validator\Rule\InstanceIsARule::class);
$validator->extend('instance_is_class', \Gzhegow\Validator\Rule\InstanceIsClassRule::class);
$validator->extend('instance_is_subclass_of', \Gzhegow\Validator\Rule\InstanceIsSubclassOfRule::class);
$validator->extend('is_a', \Gzhegow\Validator\Rule\IsARule::class);
$validator->extend('is_subclass_of', \Gzhegow\Validator\Rule\IsSubclassOfRule::class);
$validator->extend('list', \Gzhegow\Validator\Rule\ListRule::class);
//
// > а эти правила можно подключать по желанию - они используют \Gzhegow\Lib для проверок данных
// > например, чтобы числом считалась в том числе строка с числом, а не только integer
// > или, например, чтобы число больше нуля могло интерпретироваться как timestamp
$validator->extend('a_date', \Gzhegow\Validator\Rule\ADateRule::class);
$validator->extend('a_host', \Gzhegow\Validator\Rule\AHostRule::class);
$validator->extend('a_in', \Gzhegow\Validator\Rule\AInRule::class);
$validator->extend('a_int', \Gzhegow\Validator\Rule\AIntRule::class);
$validator->extend('a_link', \Gzhegow\Validator\Rule\ALinkRule::class);
$validator->extend('a_of', \Gzhegow\Validator\Rule\AOfRule::class);
$validator->extend('a_string', \Gzhegow\Validator\Rule\AStringRule::class);
$validator->extend('a_url', \Gzhegow\Validator\Rule\AUrlRule::class);
$validator->extend('a_userbool', \Gzhegow\Validator\Rule\AUserboolRule::class);
$validator->extend('a_uuid', \Gzhegow\Validator\Rule\AUuidRule::class);
//
// > и конечно, можно написать своих правил под приложение, обычно это нормальная практика
// > например, собственная проверка логина на правильность или сложность пароля, или номера кошельков и т.д.


// >>> ЗАПУСКАЕМ!

// > TEST
// > создаем валидатор и запускаем проверку
$fn = function () {
    _print('TEST 1');
    echo PHP_EOL;


    $uuid = 'e08e6870-1e13-47c0-bf86-cb0944f6f4bf';

    $builder = \Gzhegow\Validator\Validator::builder();

    $data = [
        // uuid_missing
        'uuid_null'         => null,
        'uuid_empty_string' => '',
        'uuid_wrong'        => 'test',
        'uuid'              => $uuid,
    ];

    $validation = $builder
        ->addData($data)
        ->addRulesMap([
            'uuid_missing'      => 'required|a_uuid',
            'uuid_null'         => 'required|a_uuid',
            'uuid_empty_string' => 'required|a_uuid',
            'uuid_wrong'        => 'required|a_uuid',
            'uuid'              => 'required|a_uuid',
        ])
    ;

    $validatedData = $validation->validated($input);
    $validatedAttributes = $validation->validatedAttributes();
    _print_array_multiline($validatedData, 2);
    _print_array_multiline($validatedAttributes, 2);
    echo PHP_EOL;

    $validData = $validation->valid($input);
    $validAttributes = $validation->validAttributes();
    _print_array_multiline($validData, 2);
    _print_array_multiline($validAttributes, 2);
    echo PHP_EOL;

    $invalidData = $validation->invalid($input);
    $invalidAttributes = $validation->invalidAttributes();
    _print_array_multiline($invalidData, 2);
    _print_array_multiline($invalidAttributes, 2);
    echo PHP_EOL;

    $messages = $validation->messages();
    _print_array_multiline($messages, 2);
};
_assert_stdout($fn, [], '
"TEST 1"

###
[
  "uuid" => "e08e6870-1e13-47c0-bf86-cb0944f6f4bf",
  "uuid_missing" => NULL,
  "uuid_null" => NULL,
  "uuid_empty_string" => NULL,
  "uuid_wrong" => NULL
]
###
###
[
  "uuid" => TRUE,
  "uuid_missing" => FALSE,
  "uuid_null" => TRUE,
  "uuid_empty_string" => TRUE,
  "uuid_wrong" => TRUE
]
###

###
[
  "uuid" => "e08e6870-1e13-47c0-bf86-cb0944f6f4bf"
]
###
###
[
  "uuid" => TRUE
]
###

###
[
  "uuid_null" => NULL,
  "uuid_empty_string" => "",
  "uuid_wrong" => "test",
  "uuid_missing" => NULL
]
###
###
[
  "uuid_missing" => FALSE,
  "uuid_null" => TRUE,
  "uuid_empty_string" => TRUE,
  "uuid_wrong" => TRUE
]
###

###
[
  "uuid_missing" => [
    "validation.required"
  ],
  "uuid_null" => [
    "validation.a_uuid"
  ],
  "uuid_empty_string" => [
    "validation.a_uuid"
  ],
  "uuid_wrong" => [
    "validation.a_uuid"
  ]
]
###
');


// > TEST
// > проверяем вложенный массив
$fn = function () {
    _print('TEST 2');
    echo PHP_EOL;


    $builder = \Gzhegow\Validator\Validator::builder();

    $data = [
        // uuid_missing
        'uuid_null'         => null,
        'uuid_empty_string' => '',
        'uuid_wrong'        => 'test',
        'uuid'              => \Gzhegow\Lib\Lib::random()->uuid(),
        //
        'my_nested_array'   => [
            // uuid_missing
            'uuid_null'         => null,
            'uuid_empty_string' => '',
            'uuid_wrong'        => 'test',
            'uuid'              => \Gzhegow\Lib\Lib::random()->uuid(),
            //
            'my_nested_array'   => [
                // uuid_missing
                'uuid_null'         => null,
                'uuid_empty_string' => '',
                'uuid_wrong'        => 'test',
                'uuid'              => \Gzhegow\Lib\Lib::random()->uuid(),
            ],
        ],
    ];

    $validation = $builder
        ->addData($data)
        ->addRulesMap([
            'uuid_missing'                      => 'required|a_uuid',
            'uuid_null'                         => 'required|a_uuid',
            'uuid_empty_string'                 => 'required|a_uuid',
            'uuid_wrong'                        => 'required|a_uuid',
            'uuid'                              => 'required|a_uuid',
            //
            // > можно перечислять ключи вручную
            'my_nested_array'                   => 'array',
            'my_nested_array.uuid_missing'      => 'required|a_uuid',
            'my_nested_array.uuid_null'         => 'required|a_uuid',
            'my_nested_array.uuid_empty_string' => 'required|a_uuid',
            'my_nested_array.uuid_wrong'        => 'required|a_uuid',
            'my_nested_array.uuid'              => 'required|a_uuid',
            //
            // > или можно использовать символ `*`, как в laravel, чтобы указать "все"
            // > но, надо понимать, что все означает "все имеющиеся", то есть если ключ нужен, а его нет, то проверки не будет
            'my_nested_array.my_nested_array'   => 'array',
            'my_nested_array.my_nested_array.*' => 'required|a_uuid',
        ])
    ;

    $messages = $validation->messages();
    _print_array_multiline($messages, 2);
};
_assert_stdout($fn, [], '
"TEST 2"

###
[
  "uuid_missing" => [
    "validation.required"
  ],
  "uuid_null" => [
    "validation.a_uuid"
  ],
  "uuid_empty_string" => [
    "validation.a_uuid"
  ],
  "uuid_wrong" => [
    "validation.a_uuid"
  ],
  "my_nested_array.uuid_missing" => [
    "validation.required"
  ],
  "my_nested_array.uuid_null" => [
    "validation.a_uuid"
  ],
  "my_nested_array.uuid_empty_string" => [
    "validation.a_uuid"
  ],
  "my_nested_array.uuid_wrong" => [
    "validation.a_uuid"
  ],
  "my_nested_array.my_nested_array.uuid_null" => [
    "validation.a_uuid"
  ],
  "my_nested_array.my_nested_array.uuid_empty_string" => [
    "validation.a_uuid"
  ],
  "my_nested_array.my_nested_array.uuid_wrong" => [
    "validation.a_uuid"
  ]
]
###
');


// > TEST
// > проверка механизмов фильтрации
// > как правило, после проверки данных, следует их приведение к нужным типам, например, (string) '1' часто приводится к (int) 1
$fn = function () {
    _print('TEST 3');
    echo PHP_EOL;


    $builder = \Gzhegow\Validator\Validator::builder();

    $data = [
        'my_key_int_0'  => '0',
        'my_key_int_1'  => '1',
        //
        'my_nested_key' => [
            0                        => '0',
            1                        => '1',
            'my_nested_key_subkey_0' => '0',
            'my_nested_key_subkey_1' => '1',
            //
            'my_nested_key_subkey'   => [
                0                               => '0',
                1                               => '1',
                'my_nested_key_subkey_subkey_0' => '0',
                'my_nested_key_subkey_subkey_1' => '1',
            ],
        ],
    ];

    $validation = $builder
        ->addData($data)
        ->addRules('my_key_int_0', 'required|int|min:1', 'intval')
        ->addRules('my_key_int_1', 'required|int|min:1', 'intval')
        ->addRules('my_nested_key.*', 'required|int|min:1', 'intval')
        ->addRules('my_nested_key.*.*', 'required|int|min:1', 'intval')
    ;

    $validData = $validation->validated($input);
    _print_array_multiline($validData, 3);
    echo PHP_EOL;

    $messages = $validation->messages();
    _print_array_multiline($messages, 2);
};
_assert_stdout($fn, [], '
"TEST 3"

###
[
  "my_key_int_1" => 1,
  "my_key_int_0" => NULL,
  "my_nested_key" => NULL
]
###

###
[
  "my_key_int_0" => [
    "validation.min"
  ],
  "my_nested_key.0" => [
    "validation.min"
  ],
  "my_nested_key.my_nested_key_subkey_0" => [
    "validation.min"
  ],
  "my_nested_key.my_nested_key_subkey" => [
    "validation.integer"
  ],
  "my_nested_key.my_nested_key_subkey.0" => [
    "validation.min"
  ],
  "my_nested_key.my_nested_key_subkey.my_nested_key_subkey_subkey_0" => [
    "validation.min"
  ]
]
###
');


// > TEST
// > используем механизм переводчика для получения ошибок на нужном языке
// > лучше это делать на уровне всей программы в конце, а не отдельно по-модулям
// > но laravel как всегда по-своему всех имел, поэтому я по-умолчанию выключил переводчик
$fn = function () {
    _print('TEST 4');
    echo PHP_EOL;


    $builder = \Gzhegow\Validator\Validator::builder();

    $data = [
        // uuid_missing
        'uuid_null'         => null,
        'uuid_empty_string' => '',
        'uuid_wrong'        => 'test',
        'uuid'              => \Gzhegow\Lib\Lib::random()->uuid(),
    ];

    $validation = $builder
        ->addData($data)
        ->addRulesMap([
            'uuid_missing'      => 'required|a_uuid',
            'uuid_null'         => 'required|a_uuid',
            'uuid_empty_string' => 'required|a_uuid',
            'uuid_wrong'        => 'required|a_uuid',
            'uuid'              => 'required|a_uuid',
        ])
    ;

    $validation->setLocale('en');
    $messages = $validation->messages();
    _print_array_multiline($messages, 2);
    echo PHP_EOL;

    $validation->setLocale('ru');
    $messages = $validation->messages();
    _print_array_multiline($messages, 2);
    echo PHP_EOL;
};
_assert_stdout($fn, [], '
"TEST 4"

###
[
  "uuid_missing" => [
    "The `uuid_missing` field is required."
  ],
  "uuid_null" => [
    "The `uuid_null` field must be an UUID."
  ],
  "uuid_empty_string" => [
    "The `uuid_empty_string` field must be an UUID."
  ],
  "uuid_wrong" => [
    "The `uuid_wrong` field must be an UUID."
  ]
]
###

###
[
  "uuid_missing" => [
    "Поле `uuid_missing` обязательно для заполнения."
  ],
  "uuid_null" => [
    "Поле `uuid_null` должно быть UUID."
  ],
  "uuid_empty_string" => [
    "Поле `uuid_empty_string` должно быть UUID."
  ],
  "uuid_wrong" => [
    "Поле `uuid_wrong` должно быть UUID."
  ]
]
###
');


// > TEST
// > проверка режимов API и WEB
$fn = function () {
    _print('TEST 5');
    echo PHP_EOL;


    $builder = \Gzhegow\Validator\Validator::builder();

    $data = [
        'user_sent_null'         => null,
        'user_sent_empty_string' => '',
    ];

    $validation = $builder
        ->addData($data)
        ->addRulesMap([
            'user_sent_null'         => 'required|string',
            'user_sent_empty_string' => 'required|string',
        ])
    ;

    $messages = $validation->messages();
    _print_array_multiline($messages, 2);
    echo PHP_EOL;


    $validation
        ->modeWeb(true)
        ->modeApi(false)
    ;

    $messages = $validation->messages();
    _print_array_multiline($messages, 2);
    echo PHP_EOL;


    $validation
        ->modeWeb(false)
        ->modeApi(true)
    ;

    $messages = $validation->messages();
    _print_array_multiline($messages, 2);
    echo PHP_EOL;


    $validation
        ->modeWeb(true)
        ->modeApi(true)
    ;

    $messages = $validation->messages();
    _print_array_multiline($messages, 2);
    echo PHP_EOL;
};
_assert_stdout($fn, [], '
"TEST 5"

###
[
  "user_sent_null" => [
    "validation.string"
  ]
]
###

###
[
  "user_sent_null" => [
    "validation.string"
  ],
  "user_sent_empty_string" => [
    "validation.required"
  ]
]
###

###
[
  "user_sent_null" => [
    "validation.required"
  ]
]
###

###
[
  "user_sent_null" => [
    "validation.required"
  ],
  "user_sent_empty_string" => [
    "validation.required"
  ]
]
###
');
```