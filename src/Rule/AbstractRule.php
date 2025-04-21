<?php

namespace Gzhegow\Validator\Rule;


abstract class AbstractRule implements RuleInterface
{
    /**
     * @var array
     */
    protected $parameters = [];


    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }


    public static function parse(string $ruleName, array $ruleArguments = []) : GenericRule
    {
        $ruleParameters = $ruleArguments;

        return GenericRule::fromClassAndParameters(
            static::class,
            [ 'parameters' => $ruleParameters ]
        );
    }


    public static function message(array $conditions = []) : string
    {
        return 'validation.' . static::NAME;
    }


    public function getParameters() : array
    {
        return $this->parameters;
    }
}
