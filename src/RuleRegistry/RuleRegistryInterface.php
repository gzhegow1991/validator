<?php

namespace Gzhegow\Validator\RuleRegistry;

use Gzhegow\Validator\Rule\RuleInterface;
use Gzhegow\Validator\Rule\Kit\RuleDefinitionInterface;


interface RuleRegistryInterface
{
    /**
     * @param class-string<RuleDefinitionInterface> $definition
     */
    public function register(string $definition);


    /**
     * @param class-string<RuleInterface>|null $ruleClass
     */
    public function hasRuleName(string $ruleName, ?string &$ruleClass = null) : bool;

    /**
     * @param class-string<RuleInterface> $ruleClass
     */
    public function hasRuleClass(string $ruleClass, ?string &$ruleName = null) : bool;


    /**
     * @param class-string<RuleInterface> $ruleClass
     */
    public function getRuleName(string $ruleClass) : string;

    /**
     * @return class-string<RuleInterface>
     */
    public function getRuleClass(string $ruleName) : string;


    /**
     * @param class-string<RuleInterface>[] $rules
     *
     * @return static
     */
    public function setRules(array $rules);

    /**
     * @param class-string<RuleInterface> $ruleClass
     *
     * @return static
     */
    public function addRule(string $ruleClass);


    /**
     * @param string[] $ruleNames
     *
     * @return static
     */
    public function removeRules(array $ruleNames);

    /**
     * @return static
     */
    public function removeRule(string $ruleName);
}
