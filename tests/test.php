<?php

define('__ROOT__', __DIR__ . '/..');

require_once __ROOT__ . '/vendor/autoload.php';


// > настраиваем PHP
ini_set('memory_limit', '32M');


// > настраиваем обработку ошибок
(new \Gzhegow\Lib\Exception\ErrorHandler())
    ->useErrorReporting()
    ->useErrorHandler()
    ->useExceptionHandler()
;


// > объявляем несколько функция для тестирования
$ffn = new class {
    function value_array_multiline($value, ?int $maxLevel = null, array $options = []) : string
    {
        return \Gzhegow\Lib\Lib::debug()->value_array_multiline($value, $maxLevel, $options);
    }


    function values($separator = null, ...$values) : string
    {
        return \Gzhegow\Lib\Lib::debug()->values([], $separator, ...$values);
    }


    function print(...$values) : void
    {
        echo $this->values(' | ', ...$values) . PHP_EOL;
    }

    function print_array_multiline($value, ?int $maxLevel = null, array $options = []) : void
    {
        echo $this->value_array_multiline($value, $maxLevel, $options) . PHP_EOL;
    }


    function assert_stdout(
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
};


// > создаем enum-ы для тестирования правила InEnum (только для PHP >8.1.0)
if (PHP_VERSION_ID >= 80100) {
    require_once __ROOT__ . '/tests/src/Enum/HelloWorldEnum.php';
    require_once __ROOT__ . '/tests/src/Enum/HelloWorldBackedEnum.php';
}


// >>> ЗАПУСКАЕМ!

// > сначала всегда фабрика (она также создает зарегистрированные Rule, т.е. можно подключить IoC контейнер)
$factory = new \Gzhegow\Validator\ValidatorFactory();

// > создаем процессор для вызова фильтров, поскольку отдельной сущности фильтр не предусмотрено, это может быть callable
$processor = new \Gzhegow\Validator\Processor\ValidatorProcessor(
    $factory
);

// > создаем переводчик, который преобразует ошибки и исключения валидатора на требуемый язык, подставляет параметры или меняет название атрибутов
// > по-умолчанию он просто выводит сообщение, которое возвращено из правила
// > это правильное место, чтобы внедрить свой переводчик на разные языки
$translator = new \Gzhegow\Validator\Translator\ValidatorPassTranslator();

// > создаем регистр правил, где правила проходят регистрацию
// > если вы задаете правила используя объекты и фасады - их можно не регистрировать
// > регистрация нужна ТОЛЬКО для разбора полностью текстовых правил
// > текстовая форма валидаций удобно отправляется на фронтенд при работе в команде
// > - в текстовой форме записи легко ошибится
// > - если ваши правила могут сменить названия, то тексты останутся нетронутыми
//
// > `present|string|size_min:1`
// > vs
// > `[ Rule::present(), Rule::string(), Rule::size_min(1) ]`
$registry = new \Gzhegow\Validator\RuleRegistry\RuleRegistry();
$registry->register(\Gzhegow\Validator\Rule\Kit\Rule::class);

// > можно разрегистрировать некоторые правила, чтобы заменить своими
// $registry->removeRules([ 'string' ]);
// $registry->addRule(\Gzhegow\Validator\Rule\Kit\Type\StringRule::class);

// > создаем фасад
$validator = new \Gzhegow\Validator\ValidatorFacade(
    $factory,
    $processor,
    $translator,
    //
    $registry
);

// > регистрируем фасад в статику для удобного доступа из закрытых контекстов
\Gzhegow\Validator\Validator::setFacade($validator);


// > ПРИМЕР со всеми правилами, которые идут в комплекте
//
// $validation = \Gzhegow\Validator\Validator::new();
//
// (function ($validation) {
//     $validation->addData([
//         'blank'             => null,
//         'not_blank'         => 0,
//         //
//         'present'           => null,
//         // 'not_present'              => null,
//         //
//         '_present_any_a'    => null,
//         // '_present_any_b'       => null,
//         // 'present_any'          => 0,
//         // 'presented_without_all' => 0,
//         //
//         '_present_pair_a'   => null,
//         // '_present_pair_b'      => null,
//         'present_pair'      => 0,
//         'presented_with_one' => 0,
//         //
//         '_present_side_a'   => null,
//         '_present_side_b'   => null,
//         // 'present_side'         => 0,
//         // 'presented_without_one' => 0,
//         //
//         '_present_set_a'    => null,
//         '_present_set_b'    => null,
//         'present_set'       => 0,
//         'presented_with_all' => 0,
//     ]);
//
//     $validation->addRules('blank', [ \Gzhegow\Validator\Rule\Kit\Rule::blank() ]);
//     $validation->addRules('not_blank', [ \Gzhegow\Validator\Rule\Kit\Rule::not_blank() ]);
//     //
//     $validation->addRules('present', [ \Gzhegow\Validator\Rule\Kit\Rule::present() ]);
//     $validation->addRules('not_present', [ \Gzhegow\Validator\Rule\Kit\Rule::not_present() ]);
//     //
//     $validation->addRules('present_any', [ \Gzhegow\Validator\Rule\Kit\Rule::present_any([ [ '_present_any_a', '_present_any_b' ] ]) ]);
//     $validation->addRules('presented_without_all', [ \Gzhegow\Validator\Rule\Kit\Rule::presented_without_all([ [ '_present_any_a', '_present_any_b' ] ]) ]);
//     //
//     $validation->addRules('present_pair', [ \Gzhegow\Validator\Rule\Kit\Rule::present_pair([ [ '_present_pair_a', '_present_pair_b' ] ]) ]);
//     $validation->addRules('presented_with_one', [ \Gzhegow\Validator\Rule\Kit\Rule::presented_with_one([ [ '_present_pair_a', '_present_pair_b' ] ]) ]);
//     //
//     $validation->addRules('present_side', [ \Gzhegow\Validator\Rule\Kit\Rule::present_side([ [ '_present_side_a', '_present_side_b' ] ]) ]);
//     $validation->addRules('presented_without_one', [ \Gzhegow\Validator\Rule\Kit\Rule::presented_without_one([ [ '_present_side_a', '_present_side_b' ] ]) ]);
//     //
//     $validation->addRules('present_set', [ \Gzhegow\Validator\Rule\Kit\Rule::present_set([ [ '_present_set_a', '_present_set_b' ] ]) ]);
//     $validation->addRules('presented_with_all', [ \Gzhegow\Validator\Rule\Kit\Rule::presented_with_all([ [ '_present_set_a', '_present_set_b' ] ]) ]);
// })($validation);
//
// (function ($validation) {
//     $validation->addData([
//         'diff_all'           => [ 1, 2, 3 ],
//         'diff_any'           => [ 1, 2, 3 ],
//         'intersect_all'      => [ 1, 2, 3 ],
//         'intersect_any'      => [ 1, 2, 3 ],
//         'keys_diff_all'      => [ 1 => true, 2 => true, 3 => true ],
//         'keys_diff_any'      => [ 1 => true, 2 => true, 3 => true ],
//         'keys_intersect_all' => [ 1 => true, 2 => true, 3 => true ],
//         'keys_intersect_any' => [ 1 => true, 2 => true, 3 => true ],
//         'unique'             => [ 'name1', 'Name1' ],
//     ]);
//
//     $validation->addRules('diff_all', [ \Gzhegow\Validator\Rule\Kit\Rule::diff_all([ [ 4 ] ]) ]);
//     $validation->addRules('diff_any', [ \Gzhegow\Validator\Rule\Kit\Rule::diff_any([ [ 1, 2, 3, 4 ] ]) ]);
//     $validation->addRules('intersect_all', [ \Gzhegow\Validator\Rule\Kit\Rule::intersect_all([ [ 1, 2, 3 ] ]) ]);
//     $validation->addRules('intersect_any', [ \Gzhegow\Validator\Rule\Kit\Rule::intersect_any([ [ 1 ] ]) ]);
//     $validation->addRules('keys_diff_all', [ \Gzhegow\Validator\Rule\Kit\Rule::keys_diff_all([ [ 4 ] ]) ]);
//     $validation->addRules('keys_diff_any', [ \Gzhegow\Validator\Rule\Kit\Rule::keys_diff_any([ [ 1, 2, 3, 4 ] ]) ]);
//     $validation->addRules('keys_intersect_all', [ \Gzhegow\Validator\Rule\Kit\Rule::keys_intersect_all([ [ 1, 2, 3 ] ]) ]);
//     $validation->addRules('keys_intersect_any', [ \Gzhegow\Validator\Rule\Kit\Rule::keys_intersect_any([ [ 1 ] ]) ]);
//     $validation->addRules('unique', [ \Gzhegow\Validator\Rule\Kit\Rule::unique() ]);
// })($validation);
//
// (function ($validation) {
//     $validation->addData([
//         'date_between'   => new \DateTime('@5'),
//         'date_inside'    => new \DateTime('@5'),
//         'date_eq'        => new \DateTime('@5'),
//         'date_neq'       => new \DateTime('@5'),
//         'date_gt'        => new \DateTime('@5'),
//         'date_lt'        => new \DateTime('@5'),
//         'date_max'       => new \DateTime('@5'),
//         'date_min'       => new \DateTime('@5'),
//         //
//         '_date_field_0'  => new \DateTime('@0'),
//         '_date_field_5'  => new \DateTime('@5'),
//         '_date_field_10' => new \DateTime('@10'),
//         'date_eq_field'  => new \DateTime('@5'),
//         'date_neq_field' => new \DateTime('@5'),
//         'date_gt_field'  => new \DateTime('@5'),
//         'date_lt_field'  => new \DateTime('@5'),
//         'date_max_field' => new \DateTime('@5'),
//         'date_min_field' => new \DateTime('@5'),
//     ]);
//
//     $validation->addRules('date_between', [ \Gzhegow\Validator\Rule\Kit\Rule::date_between([ '@5', '@10' ]) ]);
//     $validation->addRules('date_inside', [ \Gzhegow\Validator\Rule\Kit\Rule::date_inside([ '@0', '@10' ]) ]);
//     $validation->addRules('date_eq', [ \Gzhegow\Validator\Rule\Kit\Rule::date_eq([ '@5' ]) ]);
//     $validation->addRules('date_neq', [ \Gzhegow\Validator\Rule\Kit\Rule::date_neq([ '@0' ]) ]);
//     $validation->addRules('date_gt', [ \Gzhegow\Validator\Rule\Kit\Rule::date_gt([ '@0' ]) ]);
//     $validation->addRules('date_lt', [ \Gzhegow\Validator\Rule\Kit\Rule::date_lt([ '@10' ]) ]);
//     $validation->addRules('date_max', [ \Gzhegow\Validator\Rule\Kit\Rule::date_max([ '@5' ]) ]);
//     $validation->addRules('date_min', [ \Gzhegow\Validator\Rule\Kit\Rule::date_min([ '@5' ]) ]);
//     //
//     $validation->addRules('date_eq_field', [ \Gzhegow\Validator\Rule\Kit\Rule::date_eq_field([ '_date_field_5' ]) ]);
//     $validation->addRules('date_neq_field', [ \Gzhegow\Validator\Rule\Kit\Rule::date_neq_field([ '_date_field_0' ]) ]);
//     $validation->addRules('date_gt_field', [ \Gzhegow\Validator\Rule\Kit\Rule::date_gt_field([ '_date_field_0' ]) ]);
//     $validation->addRules('date_lt_field', [ \Gzhegow\Validator\Rule\Kit\Rule::date_lt_field([ '_date_field_10' ]) ]);
//     $validation->addRules('date_max_field', [ \Gzhegow\Validator\Rule\Kit\Rule::date_max_field([ '_date_field_5' ]) ]);
//     $validation->addRules('date_min_field', [ \Gzhegow\Validator\Rule\Kit\Rule::date_min_field([ '_date_field_5' ]) ]);
// })($validation);
//
// (function ($validation) {
//     $validation->addData([
//         'size_between' => [ 1, 1, 1 ],
//         'size_min'     => [ 1, 1, 1 ],
//         'size_max'     => [ 1, 1, 1 ],
//         'size'         => [ 1, 1, 1 ],
//     ]);
//
//     $validation->addRules('size_between', [ \Gzhegow\Validator\Rule\Kit\Rule::size_between([ 3, 3 ]) ]);
//     $validation->addRules('size_min', [ \Gzhegow\Validator\Rule\Kit\Rule::size_min([ 3 ]) ]);
//     $validation->addRules('size_max', [ \Gzhegow\Validator\Rule\Kit\Rule::size_max([ 3 ]) ]);
//     $validation->addRules('size', [ \Gzhegow\Validator\Rule\Kit\Rule::size([ 3 ]) ]);
// })($validation);
//
// (function ($validation) {
//     $validation->addData([
//         'between'   => 'b',
//         'inside'    => 'b',
//         'eq'        => 'b',
//         'neq'       => 'b',
//         'gt'        => 'b',
//         'lt'        => 'b',
//         'lte'       => 'b',
//         'gte'       => 'b',
//         //
//         '_field_a'  => 'a',
//         '_field_b'  => 'b',
//         '_field_c'  => 'c',
//         'eq_field'  => 'b',
//         'neq_field' => 'b',
//         'gt_field'  => 'b',
//         'lt_field'  => 'b',
//         'lte_field' => 'b',
//         'gte_field' => 'b',
//     ]);
//
//     $validation->addRules('between', [ \Gzhegow\Validator\Rule\Kit\Rule::between([ 'b', 'c' ]) ]);
//     $validation->addRules('inside', [ \Gzhegow\Validator\Rule\Kit\Rule::inside([ 'a', 'c' ]) ]);
//     $validation->addRules('eq', [ \Gzhegow\Validator\Rule\Kit\Rule::eq([ 'b' ]) ]);
//     $validation->addRules('neq', [ \Gzhegow\Validator\Rule\Kit\Rule::neq([ 'a' ]) ]);
//     $validation->addRules('gt', [ \Gzhegow\Validator\Rule\Kit\Rule::gt([ 'a' ]) ]);
//     $validation->addRules('lt', [ \Gzhegow\Validator\Rule\Kit\Rule::lt([ 'c' ]) ]);
//     $validation->addRules('lte', [ \Gzhegow\Validator\Rule\Kit\Rule::lte([ 'b' ]) ]);
//     $validation->addRules('gte', [ \Gzhegow\Validator\Rule\Kit\Rule::gte([ 'b' ]) ]);
//     //
//     $validation->addRules('eq_field', [ \Gzhegow\Validator\Rule\Kit\Rule::eq_field([ '_field_b' ]) ]);
//     $validation->addRules('neq_field', [ \Gzhegow\Validator\Rule\Kit\Rule::neq_field([ '_field_a' ]) ]);
//     $validation->addRules('gt_field', [ \Gzhegow\Validator\Rule\Kit\Rule::gt_field([ '_field_a' ]) ]);
//     $validation->addRules('lt_field', [ \Gzhegow\Validator\Rule\Kit\Rule::lt_field([ '_field_c' ]) ]);
//     $validation->addRules('lte_field', [ \Gzhegow\Validator\Rule\Kit\Rule::lte_field([ '_field_b' ]) ]);
//     $validation->addRules('gte_field', [ \Gzhegow\Validator\Rule\Kit\Rule::gte_field([ '_field_b' ]) ]);
// })($validation);
//
// (function ($validation) {
//     $validation->addData([
//         'json' => json_encode([ 1, 2, 3 ]),
//     ]);
//
//     $validation->addRules('json', [ \Gzhegow\Validator\Rule\Kit\Rule::json() ]);
// })($validation);
//
// (function ($validation) {
//     $validation->addData([
//         '_field_in'    => [ 1, 2, 3 ],
//         'in_field'     => 1,
//         'in_not_field' => 4,
//         //
//         'in_enum'      => 1,
//         'in_not_enum'  => 4,
//         'in_not'       => 4,
//         'in'           => 1,
//     ]);
//
//     $validation->addRules('in_field', [ \Gzhegow\Validator\Rule\Kit\Rule::in_field([ '../_field_in' ]) ]);
//     $validation->addRules('in_not_field', [ \Gzhegow\Validator\Rule\Kit\Rule::in_not_field([ '../_field_in' ]) ]);
//     //
//     $validation->addRules('in_enum', [ \Gzhegow\Validator\Rule\Kit\Rule::in_enum([ '\Gzhegow\Validator\Tests\Enum\HelloWorldBackedEnum' ]) ]);
//     $validation->addRules('in_not_enum', [ \Gzhegow\Validator\Rule\Kit\Rule::in_not_enum([ '\Gzhegow\Validator\Tests\Enum\HelloWorldBackedEnum' ]) ]);
//     $validation->addRules('in_not', [ \Gzhegow\Validator\Rule\Kit\Rule::in_not([ [ 1, 2, 3 ] ]) ]);
//     $validation->addRules('in', [ \Gzhegow\Validator\Rule\Kit\Rule::in([ [ 1, 2, 3 ] ]) ]);
// })($validation);
//
// (function ($validation) {
//     $validation->addData([
//         'ip_in_subnets'    => '127.0.0.1',
//         'ip_in_subnets_v4' => '127.0.0.1',
//         'ip_in_subnets_v6' => '::1',
//     ]);
//
//     $validation->addRules('ip_in_subnets', [ \Gzhegow\Validator\Rule\Kit\Rule::ip_in_subnets([ '127.0.0.1/32' ]) ]);
//     $validation->addRules('ip_in_subnets_v4', [ \Gzhegow\Validator\Rule\Kit\Rule::ip_in_subnets_v4([ '127.0.0.1/32' ]) ]);
//     $validation->addRules('ip_in_subnets_v6', [ \Gzhegow\Validator\Rule\Kit\Rule::ip_in_subnets_v6([ '::1/128' ]) ]);
// })($validation);
//
// (function ($validation) {
//     $validation->addData([
//         'is_of_a'            => new \DateTime('@5'),
//         'is_of_class'        => new \DateTime('@5'),
//         'is_of_subclass'     => new \DateTime('@5'),
//         'struct_is_a'        => \DateTime::class,
//         'struct_is_class'    => \DateTime::class,
//         'struct_is_subclass' => \DateTime::class,
//     ]);
//
//     $validation->addRules('is_of_a', [ \Gzhegow\Validator\Rule\Kit\Rule::is_of_a([ \DateTimeInterface::class ]) ]);
//     $validation->addRules('is_of_class', [ \Gzhegow\Validator\Rule\Kit\Rule::is_of_class([ \DateTime::class ]) ]);
//     $validation->addRules('is_of_subclass', [ \Gzhegow\Validator\Rule\Kit\Rule::is_of_subclass([ \DateTimeInterface::class ]) ]);
//     $validation->addRules('struct_is_a', [ \Gzhegow\Validator\Rule\Kit\Rule::struct_is_a([ \DateTimeInterface::class ]) ]);
//     $validation->addRules('struct_is_class', [ \Gzhegow\Validator\Rule\Kit\Rule::struct_is_class([ \DateTime::class ]) ]);
//     $validation->addRules('struct_is_subclass', [ \Gzhegow\Validator\Rule\Kit\Rule::struct_is_subclass([ \DateTimeInterface::class ]) ]);
// })($validation);
//
// (function ($validation) {
//     $validation->addData([
//         'contains'    => 'hello123',
//         'ctype_alnum' => 'hello123',
//         'ctype_alpha' => 'hello',
//         'ctype_digit' => '123',
//         'ends'        => 'hello123',
//         'regex_not'   => '123',
//         'regex'       => 'hello123',
//         'starts'      => 'hello123',
//     ]);
//
//     $validation->addRules('contains', [ \Gzhegow\Validator\Rule\Kit\Rule::contains([ 'ello1' ]) ]);
//     $validation->addRules('ctype_alnum', [ \Gzhegow\Validator\Rule\Kit\Rule::ctype_alnum() ]);
//     $validation->addRules('ctype_alpha', [ \Gzhegow\Validator\Rule\Kit\Rule::ctype_alpha() ]);
//     $validation->addRules('ctype_digit', [ \Gzhegow\Validator\Rule\Kit\Rule::ctype_digit() ]);
//     $validation->addRules('ends', [ \Gzhegow\Validator\Rule\Kit\Rule::ends([ '123' ]) ]);
//     $validation->addRules('regex_not', [ \Gzhegow\Validator\Rule\Kit\Rule::regex_not([ '[a-z]' ]) ]);
//     $validation->addRules('regex', [ \Gzhegow\Validator\Rule\Kit\Rule::regex([ '[a-z0-9]' ]) ]);
//     $validation->addRules('starts', [ \Gzhegow\Validator\Rule\Kit\Rule::starts([ 'hello' ]) ]);
// })($validation);
//
// (function ($validation) {
//     $validation->addData([
//         'array'      => [ 0 => 1, 2 => 2, 'key3' => 3 ],
//         'dict'       => [ 'key1' => 1, 'key2' => 2, 'key3' => 3 ],
//         'gettype'    => 1.0,
//         'list'       => [ 1, 2, 3 ],
//         'object'     => new \stdClass(),
//         'uuid'       => 'c511ee5f-2351-4b92-a544-769ce1eddfea',
//     ]);
//
//     $validation->addRules('array', [ \Gzhegow\Validator\Rule\Kit\Rule::array() ]);
//     $validation->addRules('dict', [ \Gzhegow\Validator\Rule\Kit\Rule::dict() ]);
//     $validation->addRules('gettype', [ \Gzhegow\Validator\Rule\Kit\Rule::gettype([ 'double' ]) ]);
//     $validation->addRules('list', [ \Gzhegow\Validator\Rule\Kit\Rule::list() ]);
//     $validation->addRules('object', [ \Gzhegow\Validator\Rule\Kit\Rule::object() ]);
//     $validation->addRules('uuid', [ \Gzhegow\Validator\Rule\Kit\Rule::uuid() ]);
// })($validation);
//
// (function ($validation) {
//     $validation->addData([
//         'date'            => '1970-01-01',
//         'date_tz_named'   => '1970-01-01 EET',
//         'date_tz_offset'  => '1970-01-01 +0100',
//         'date_tz'         => '1970-01-01 EET',
//         'interval'        => 'P1D',
//         'timezone_named'  => 'EET',
//         'timezone_offset' => '+0100',
//         'timezone'        => 'EET',
//     ]);
//
//     $validation->addRules('date', [ \Gzhegow\Validator\Rule\Kit\Rule::date([ 'Y-m-d' ]) ]);
//     $validation->addRules('date_tz_named', [ \Gzhegow\Validator\Rule\Kit\Rule::date_tz_named([ 'Y-m-d T' ]) ]);
//     $validation->addRules('date_tz_offset', [ \Gzhegow\Validator\Rule\Kit\Rule::date_tz_offset([ 'Y-m-d O' ]) ]);
//     $validation->addRules('date_tz', [ \Gzhegow\Validator\Rule\Kit\Rule::date_tz([ 'Y-m-d T' ]) ]);
//     $validation->addRules('interval', [ \Gzhegow\Validator\Rule\Kit\Rule::interval() ]);
//     $validation->addRules('timezone_named', [ \Gzhegow\Validator\Rule\Kit\Rule::timezone_named() ]);
//     $validation->addRules('timezone_offset', [ \Gzhegow\Validator\Rule\Kit\Rule::timezone_offset() ]);
//     $validation->addRules('timezone', [ \Gzhegow\Validator\Rule\Kit\Rule::timezone() ]);
// })($validation);
//
// (function ($validation) {
//     $validation->addData([
//         'file'  => __ROOT__ . '/tests/var/file.txt',
//         'image' => __ROOT__ . '/tests/var/file.jpg',
//     ]);
//
//     $validation->addRules('file', [ \Gzhegow\Validator\Rule\Kit\Rule::file([ [ 'txt' ], [ 'text/' ] ]) ]);
//     $validation->addRules('image', [ \Gzhegow\Validator\Rule\Kit\Rule::image([ [ 'jpg' ], [ 'image/' ] ]) ]);
// })($validation);
//
// (function ($validation) {
//     $validation->addData([
//         'address_ip'    => '127.0.0.1',
//         'address_ip_v4' => '127.0.0.1',
//         'address_ip_v6' => '::1',
//         'address_mac'   => '00-B0-D0-63-C2-26',
//         'subnet'        => '127.0.0.1/32',
//         'subnet_v4'     => '127.0.0.1/32',
//         'subnet_v6'     => '::1/128',
//     ]);
//
//     $validation->addRules('address_ip', [ \Gzhegow\Validator\Rule\Kit\Rule::address_ip() ]);
//     $validation->addRules('address_ip_v4', [ \Gzhegow\Validator\Rule\Kit\Rule::address_ip_v4() ]);
//     $validation->addRules('address_ip_v6', [ \Gzhegow\Validator\Rule\Kit\Rule::address_ip_v6() ]);
//     $validation->addRules('address_mac', [ \Gzhegow\Validator\Rule\Kit\Rule::address_mac() ]);
//     $validation->addRules('subnet', [ \Gzhegow\Validator\Rule\Kit\Rule::subnet() ]);
//     $validation->addRules('subnet_v4', [ \Gzhegow\Validator\Rule\Kit\Rule::subnet_v4() ]);
//     $validation->addRules('subnet_v6', [ \Gzhegow\Validator\Rule\Kit\Rule::subnet_v6() ]);
// })($validation);
//
// (function ($validation) {
//     $validation->addData([
//         'email' => 'name@gmail.com',
//         // 'phone_real'      => '+375 (29) 123-45-67',
//         'phone' => '+375 (29) 123-45-67',
//         // 'tel_real'        => '+375291234567',
//         'tel'   => '+375291234567',
//     ]);
//
//     $validation->addRules('email', [ \Gzhegow\Validator\Rule\Kit\Rule::email() ]);
//     // $validation->addRules('phone_real', [ \Gzhegow\Validator\Rule\Kit\Rule::phone_real() ]);
//     $validation->addRules('phone', [ \Gzhegow\Validator\Rule\Kit\Rule::phone() ]);
//     // $validation->addRules('tel_real', [ \Gzhegow\Validator\Rule\Kit\Rule::tel_real() ]);
//     $validation->addRules('tel', [ \Gzhegow\Validator\Rule\Kit\Rule::tel() ]);
// })($validation);
//
// (function ($validation) {
//     $validation->addData([
//         'host' => 'https://google.com',
//         'link' => 'my/url/link',
//         'url'  => 'https://google.com/my/url/link',
//     ]);
//
//     $validation->addRules('host', [ \Gzhegow\Validator\Rule\Kit\Rule::host() ]);
//     $validation->addRules('link', [ \Gzhegow\Validator\Rule\Kit\Rule::link() ]);
//     $validation->addRules('url', [ \Gzhegow\Validator\Rule\Kit\Rule::url() ]);
// })($validation);
//
// (function ($validation) {
//     $validation->addData([
//         'bool'           => true,
//         'boolean'        => true,
//         'decimal'        => '1.10',
//         'double'         => 1.01,
//         'float'          => 1.01,
//         'int'            => 1,
//         'integer'        => 1,
//         'num'            => 1.0,
//         'numeric'        => '1',
//         'numeric_float'  => '1.01',
//         'numeric_int'    => '1.0',
//         'string'         => 'hello',
//         'trim'           => ' hello ',
//         //
//         // > yes/y, no/n, true, false, 1, 0
//         'userbool'       => 'yes',
//         'userbool_false' => '0',
//         'userbool_true'  => '1',
//     ]);
//
//     $validation->addRules('bool', [ \Gzhegow\Validator\Rule\Kit\Rule::bool() ]);
//     $validation->addRules('boolean', [ \Gzhegow\Validator\Rule\Kit\Rule::boolean() ]);
//     $validation->addRules('decimal', [ \Gzhegow\Validator\Rule\Kit\Rule::decimal([ 2 ]) ]);
//     $validation->addRules('double', [ \Gzhegow\Validator\Rule\Kit\Rule::double() ]);
//     $validation->addRules('float', [ \Gzhegow\Validator\Rule\Kit\Rule::float() ]);
//     $validation->addRules('int', [ \Gzhegow\Validator\Rule\Kit\Rule::int() ]);
//     $validation->addRules('integer', [ \Gzhegow\Validator\Rule\Kit\Rule::integer() ]);
//     $validation->addRules('num', [ \Gzhegow\Validator\Rule\Kit\Rule::num() ]);
//     $validation->addRules('numeric', [ \Gzhegow\Validator\Rule\Kit\Rule::numeric() ]);
//     $validation->addRules('numeric_float', [ \Gzhegow\Validator\Rule\Kit\Rule::numeric_float() ]);
//     $validation->addRules('numeric_int', [ \Gzhegow\Validator\Rule\Kit\Rule::numeric_int() ]);
//     $validation->addRules('string', [ \Gzhegow\Validator\Rule\Kit\Rule::string() ]);
//     $validation->addRules('trim', [ \Gzhegow\Validator\Rule\Kit\Rule::trim() ]);
//     //
//     $validation->addRules('userbool', [ \Gzhegow\Validator\Rule\Kit\Rule::userbool() ]);
//     $validation->addRules('userbool_false', [ \Gzhegow\Validator\Rule\Kit\Rule::userbool_false() ]);
//     $validation->addRules('userbool_true', [ \Gzhegow\Validator\Rule\Kit\Rule::userbool_true() ]);
// })($validation);
//
// $validation->passes();


// >>> Тесты

// > TEST
// > создаем валидатор и запускаем проверку
$fn = function () use ($ffn) {
    $ffn->print('TEST 1');
    echo PHP_EOL;


    $uuid = 'e08e6870-1e13-47c0-bf86-cb0944f6f4bf';

    $validation = \Gzhegow\Validator\Validator::new();

    $data = [
        // uuid_missing
        'uuid_null'         => null,
        'uuid_empty_string' => '',
        'uuid_wrong'        => 'test',
        'uuid'              => $uuid,
    ];

    $validation = $validation
        ->addData($data)
        ->addRulesMap([
            'uuid_missing'      => 'present|uuid',
            'uuid_null'         => 'present|uuid',
            'uuid_empty_string' => 'present|uuid',
            'uuid_wrong'        => 'present|uuid',
            'uuid'              => 'present|uuid',
        ])
    ;

    $messages = $validation->messages();
    $ffn->print_array_multiline($messages, 2);
};
$ffn->assert_stdout($fn, [], '
"TEST 1"

###
[
  "uuid_missing" => [
    "validation.present"
  ],
  "uuid_null" => [
    "validation.uuid"
  ],
  "uuid_empty_string" => [
    "validation.uuid"
  ],
  "uuid_wrong" => [
    "validation.uuid"
  ]
]
###
');


// > TEST
// > проверяем вложенный массив
$fn = function () use ($ffn) {
    $ffn->print('TEST 2');
    echo PHP_EOL;


    $validation = \Gzhegow\Validator\Validator::new();

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

    $validation = $validation
        ->addData($data)
        ->addRulesMap([
            'uuid_missing'      => 'present|uuid',
            'uuid_null'         => 'present|uuid',
            'uuid_empty_string' => 'present|uuid',
            'uuid_wrong'        => 'present|uuid',
            'uuid'              => 'present|uuid',
        ])
        ->addRulesMap([
            // > можно перечислять ключи вручную
            'my_nested_array'                   => 'array',
            'my_nested_array.uuid_missing'      => 'present|uuid',
            'my_nested_array.uuid_null'         => 'present|uuid',
            'my_nested_array.uuid_empty_string' => 'present|uuid',
            'my_nested_array.uuid_wrong'        => 'present|uuid',
            'my_nested_array.uuid'              => 'present|uuid',
            //
            // > или можно использовать символ `*`, чтобы указать "все"
            // > но, надо понимать, что все означает "все имеющиеся", то есть если ключ нужен, а его нет, то проверки не будет
            'my_nested_array.my_nested_array'   => 'array',
            'my_nested_array.my_nested_array.*' => 'present|uuid',
        ])
        ->addRulesMap([
            // > или можно использовать символ `*`, чтобы указать "все"
            // > но, надо понимать, что все означает "все имеющиеся", то есть если ключ нужен, а его нет, то проверки не будет
            'my_nested_array.my_nested_array'   => 'array',
            'my_nested_array.my_nested_array.*' => 'present|uuid',
        ])
    ;

    $messages = $validation->messages();
    $ffn->print_array_multiline($messages, 2);

    $rules = $validation->rules();
    $ffn->print_array_multiline($rules, 2);
};
$ffn->assert_stdout($fn, [], '
"TEST 2"

###
[
  "uuid_missing" => [
    "validation.present"
  ],
  "uuid_null" => [
    "validation.uuid"
  ],
  "uuid_empty_string" => [
    "validation.uuid"
  ],
  "uuid_wrong" => [
    "validation.uuid"
  ],
  "my_nested_array.uuid_missing" => [
    "validation.present"
  ],
  "my_nested_array.uuid_null" => [
    "validation.uuid"
  ],
  "my_nested_array.uuid_empty_string" => [
    "validation.uuid"
  ],
  "my_nested_array.uuid_wrong" => [
    "validation.uuid"
  ],
  "my_nested_array.my_nested_array.uuid_null" => [
    "validation.uuid"
  ],
  "my_nested_array.my_nested_array.uuid_empty_string" => [
    "validation.uuid"
  ],
  "my_nested_array.my_nested_array.uuid_wrong" => [
    "validation.uuid"
  ]
]
###
###
[
  "uuid_missing" => "present|uuid",
  "uuid_null" => "present|uuid",
  "uuid_empty_string" => "present|uuid",
  "uuid_wrong" => "present|uuid",
  "uuid" => "present|uuid",
  "my_nested_array" => "array",
  "my_nested_array.uuid_missing" => "present|uuid",
  "my_nested_array.uuid_null" => "present|uuid",
  "my_nested_array.uuid_empty_string" => "present|uuid",
  "my_nested_array.uuid_wrong" => "present|uuid",
  "my_nested_array.uuid" => "present|uuid",
  "my_nested_array.my_nested_array" => "array",
  "my_nested_array.my_nested_array.*" => "present|uuid"
]
###
');


// > TEST
// > проверка механизмов фильтрации
// > как правило, после проверки данных, следует их приведение к нужным типам
// > например, (string) '1' часто приводится к (int) 1
$fn = function () use ($ffn) {
    $ffn->print('TEST 3');
    echo PHP_EOL;


    $validation = \Gzhegow\Validator\Validator::new();

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

    $validation = $validation
        ->addData($data)
        ->addRules('my_key_int_0', 'present|int|gte:1', 'intval')
        ->addRules('my_key_int_1', 'present|int|gte:1', 'intval')
        ->addRules('my_nested_key.*', 'present|int|gte:1', 'intval')
        ->addRules('my_nested_key.*.*', 'present|int|gte:1', 'intval')
    ;

    $messages = $validation->messages();
    $ffn->print_array_multiline($messages, 2);
};
$ffn->assert_stdout($fn, [], '
"TEST 3"

###
[
  "my_key_int_0" => [
    "validation.gte"
  ],
  "my_nested_key.0" => [
    "validation.gte"
  ],
  "my_nested_key.my_nested_key_subkey_0" => [
    "validation.gte"
  ],
  "my_nested_key.my_nested_key_subkey" => [
    "validation.int"
  ],
  "my_nested_key.my_nested_key_subkey.0" => [
    "validation.gte"
  ],
  "my_nested_key.my_nested_key_subkey.my_nested_key_subkey_subkey_0" => [
    "validation.gte"
  ]
]
###
');


// > TEST
// > проверка режимов API и WEB
$fn = function () use ($ffn) {
    $ffn->print('TEST 4');
    echo PHP_EOL;


    $builder = \Gzhegow\Validator\Validator::new();

    $data = [
        'user_sent_null'         => null,
        'user_sent_empty_string' => '',
    ];

    $validation = $builder
        ->addData($data)
        ->addRulesMap([
            'user_sent_null'         => 'present|string',
            'user_sent_empty_string' => 'present|string',
        ])
    ;


    $validation
        ->modeWeb(false)
        ->modeApi(false)
    ;

    $messages = $validation->messages();
    $ffn->print_array_multiline($messages, 2);
    echo PHP_EOL;


    $validation
        ->modeWeb(true)
        ->modeApi(false)
    ;

    $messages = $validation->messages();
    $ffn->print_array_multiline($messages, 2);
    echo PHP_EOL;


    $validation
        ->modeWeb(false)
        ->modeApi(true)
    ;

    $messages = $validation->messages();
    $ffn->print_array_multiline($messages, 2);
    echo PHP_EOL;


    $validation
        ->modeWeb(true)
        ->modeApi(true)
    ;

    $messages = $validation->messages();
    $ffn->print_array_multiline($messages, 2);
    echo PHP_EOL;
};
$ffn->assert_stdout($fn, [], '
"TEST 4"

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
    "validation.present"
  ]
]
###

###
[
  "user_sent_null" => [
    "validation.present"
  ]
]
###

###
[
  "user_sent_null" => [
    "validation.present"
  ],
  "user_sent_empty_string" => [
    "validation.present"
  ]
]
###
');


// > TEST
// > проверка разных способов указания нескольких правил и режимов маппинга на массив и объект1
$fn = function () use ($ffn) {
    $ffn->print('TEST 5');
    echo PHP_EOL;


    $validation = \Gzhegow\Validator\Validator::new();

    $validation->addData([
        'users' => [
            '21' => [
                'id'   => null,
                'code' => null,
                'name' => null,
            ],
            '22' => [
                'id'   => null,
                'code' => null,
                'name' => null,
            ],
        ],
    ]);

    $validation->addRules('users.*.id', 'present|string|size_min:1');
    $validation->addFilters('users.*.id', 'intval');
    $validation->addDefault('users.*.id', [ '0' ]);

    $validation->addRules(
        $keyWildcard = 'users.*.name',
        $rules = 'present|string|size_min:1',
        $filter = 'strval',
        $default = [ 'User' ]
    );

    $validation->addRulesMap(
        $rules = [
            'users.21.code' => [ 'present', 'string|size_min:1' ],
            'users.22.code' => [
                \Gzhegow\Validator\Rule\Kit\Rule::present(),
                \Gzhegow\Validator\Rule\Kit\Rule::string(),
                \Gzhegow\Validator\Rule\Kit\Rule::size_min([ 1 ]),
            ],
        ]
    );

    $status = $validation->passes();
    $ffn->print($status);

    $errors = $validation->errors();
    $ffn->print_array_multiline($errors, 3);

    $messages = $validation->messages();
    $ffn->print_array_multiline($messages, 2);

    echo PHP_EOL;


    $allAttributes = $validation->allAttributes();
    $validAttributes = $validation->validAttributes();
    $invalidAttributes = $validation->invalidAttributes();
    $touchedAttributes = $validation->touchedAttributes();
    $validatedAttributes = $validation->validatedAttributes();
    $ffn->print_array_multiline($allAttributes, 2);
    $ffn->print_array_multiline($validAttributes, 2);
    $ffn->print_array_multiline($invalidAttributes, 2);
    $ffn->print_array_multiline($touchedAttributes, 2);
    $ffn->print_array_multiline($validatedAttributes, 2);

    // $touched = $validation->touched();
    // $invalid = $validation->invalid();
    // $valid = $validation->valid();
    // $validated = $validation->validated();
    // $all = $validation->all();

    echo PHP_EOL;


    $bindArray = [];
    $validation->valid($bindArray);
    $ffn->print_array_multiline($bindArray, 3);

    $bindObject = new \stdClass();
    $validation->valid($bindObject);
    $ffn->print_array_multiline((array) $bindObject, 2);
};
$ffn->assert_stdout($fn, [], '
"TEST 5"

FALSE
###
[
  "users.21.name" => [
    [
      "message" => "validation.size_min",
      "throwable" => NULL,
      "value" => "{ array(1) }",
      "key" => "name",
      "path" => "{ array(3) }",
      "rule" => "{ object # Gzhegow\Validator\Rule\Kit\Main\Cmp\Size\SizeMinRule }",
      "parameters" => "{ array(1) }"
    ]
  ],
  "users.22.name" => [
    [
      "message" => "validation.size_min",
      "throwable" => NULL,
      "value" => "{ array(1) }",
      "key" => "name",
      "path" => "{ array(3) }",
      "rule" => "{ object # Gzhegow\Validator\Rule\Kit\Main\Cmp\Size\SizeMinRule }",
      "parameters" => "{ array(1) }"
    ]
  ],
  "users.21.code" => [
    [
      "message" => "validation.string",
      "throwable" => NULL,
      "value" => "{ array(1) }",
      "key" => "code",
      "path" => "{ array(3) }",
      "rule" => "{ object # Gzhegow\Validator\Rule\Kit\Type\StringRule }",
      "parameters" => []
    ]
  ],
  "users.22.code" => [
    [
      "message" => "validation.string",
      "throwable" => NULL,
      "value" => "{ array(1) }",
      "key" => "code",
      "path" => "{ array(3) }",
      "rule" => "{ object # Gzhegow\Validator\Rule\Kit\Type\StringRule }",
      "parameters" => []
    ]
  ]
]
###
###
[
  "users.21.name" => [
    "validation.size_min"
  ],
  "users.22.name" => [
    "validation.size_min"
  ],
  "users.21.code" => [
    "validation.string"
  ],
  "users.22.code" => [
    "validation.string"
  ]
]
###

###
[
  "users.21.id" => 0,
  "users.22.id" => 0,
  "users.21.name" => NULL,
  "users.22.name" => NULL,
  "users.21.code" => NULL,
  "users.22.code" => NULL
]
###
###
[
  "users.21.id" => 0,
  "users.22.id" => 0
]
###
###
[
  "users.21.name" => NULL,
  "users.22.name" => NULL,
  "users.21.code" => NULL,
  "users.22.code" => NULL
]
###
###
[
  "users.21.id" => NULL,
  "users.22.id" => NULL,
  "users.21.name" => NULL,
  "users.22.name" => NULL,
  "users.21.code" => NULL,
  "users.22.code" => NULL
]
###
###
[
  "users.21.id" => 0,
  "users.22.id" => 0
]
###

###
[
  "users" => [
    21 => [
      "id" => 0
    ],
    22 => [
      "id" => 0
    ]
  ]
]
###
###
[
  "users" => (object) [
    21 => (object) [
      "id" => 0
    ],
    22 => (object) [
      "id" => 0
    ]
  ]
]
###
');
