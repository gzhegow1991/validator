<?php

namespace Gzhegow\Validator\Validation;

use Gzhegow\Validator\Rule\GenericRule;
use Gzhegow\Validator\Rule\RuleInterface;
use Gzhegow\Validator\Filter\GenericFilter;
use Gzhegow\Validator\Exception\Runtime\ValidationException;
use Gzhegow\Validator\Exception\Runtime\InspectionException;


interface ValidationInterface
{
    // /**
    //  * @return static
    //  */
    // public function merge(ValidationInterface $validation);
    //
    //
    // public function has($path, &$value = null) : bool;
    //
    // /**
    //  * @return mixed
    //  */
    // public function get($path, array $fallback = []);
    //
    //
    // public function hasFiltered($path, &$value = null) : bool;
    //
    // /**
    //  * @return mixed
    //  */
    // public function getFiltered($path, array $fallback = []);
    //
    //
    // public function hasDefault($path, &$value = null) : bool;
    //
    // /**
    //  * @return mixed
    //  */
    // public function getDefault($path, array $fallback = []);
    //
    //
    // public function hasOriginal($path, &$value = null) : bool;
    //
    // /**
    //  * @return mixed
    //  */
    // public function getOriginal($path, array $fallback = []);
    //
    //
    // /**
    //  * @throws ValidationException
    //  */
    // public function validate(&$bind = null) : array;
    //
    // /**
    //  * @throws InspectionException
    //  */
    // public function inspect(&$bind = null) : array;
    //
    //
    // public function passes() : bool;
    //
    // public function errors() : array;
    //
    // public function messages() : array;
    //
    //
    // public function getData() : array;
    //
    // public function data(array $fillKeys = []) : array;
    //
    //
    // public function getRules() : array;
    //
    // public function rules(array $fillKeys = []) : array;
    //
    //
    // public function all(&$bind = null) : array;
    //
    // public function allAttributes(array $fillKeys = []) : array;
    //
    //
    // public function valid(&$bind = null) : array;
    //
    // public function validAttributes(array $fillKeys = []) : array;
    //
    //
    // public function invalid(&$bind = null) : array;
    //
    // public function invalidAttributes(array $fillKeys = []) : array;
    //
    //
    // public function touched(&$bind = null) : array;
    //
    // public function touchedAttributes(array $fillKeys = []) : array;
    //
    //
    // public function validated(&$bind = null) : array;
    //
    // public function validatedAttributes(array $fillKeys = []) : array;
    //
    //
    // /**
    //  * @return static
    //  */
    // public function modeApi(?bool $modeApi = null);
    //
    // /**
    //  * @return static
    //  */
    // public function modeWeb(?bool $modeWeb = null);
    //
    //
    // /**
    //  * @return static
    //  */
    // public function addData(array $data);
    //
    //
    // /**
    //  * @template T of string|RuleInterface|GenericRule
    //  * @template TT of callable|GenericFilter
    //  *
    //  * @param array<string, T|T[]>   $rules
    //  * @param array<string, TT|TT[]> $filters
    //  * @param array<string, mixed>   $defaults
    //  *
    //  * @return static
    //  */
    // public function addRulesMap(array $rules = [], array $filters = [], array $defaults = []);
    //
    // /**
    //  * @param array<string|RuleInterface>|string|RuleInterface $rules
    //  * @param callable|GenericFilter                           $filter
    //  * @param array{ 0?: mixed }                               $default
    //  *
    //  * @return static
    //  */
    // public function addRules(string $rulepathString, $rules, $filter = null, array $default = []);
    //
    //
    // /**
    //  * @param callable|GenericFilter        $filter
    //  * @param array<callable|GenericFilter> $filters
    //  *
    //  * @return static
    //  */
    // public function addFilters(string $rulepathString, $filter, ...$filters);
    //
    // /**
    //  * @return static
    //  */
    // public function addDefault(string $rulepathString, array $default);
    //
    //
    // public function fieldpath($path, ...$pathes) : array;
    //
    // public function fieldpathOrAbsolute($path, $pathCurrent) : array;
}
