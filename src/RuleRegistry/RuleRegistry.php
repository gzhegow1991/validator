<?php

namespace Gzhegow\Validator\RuleRegistry;

use Gzhegow\Validator\Rule\RuleInterface;
use Gzhegow\Validator\Exception\LogicException;
use Gzhegow\Validator\Rule\Kit\RuleDefinitionInterface;


class RuleRegistry implements RuleRegistryInterface
{
    /**
     * @var array<string, class-string<RuleInterface>>
     */
    protected $ruleNameIndex = [];
    /**
     * @var array<class-string<RuleInterface>, string>
     */
    protected $ruleClassIndex = [];


    /**
     * @param class-string<RuleDefinitionInterface> $definition
     */
    public function register(string $definition)
    {
        $rules = $definition::rules();

        foreach ( $rules as $i => $ruleClass ) {
            if (is_string($i)) {
                $ruleClass = $i;
            }

            $this->addRule($ruleClass);
        }

        return $this;
    }


    /**
     * @param class-string<RuleInterface>|null $ruleClass
     */
    public function hasRuleName(string $ruleName, ?string &$ruleClass = null) : bool
    {
        $ruleClass = null;

        $status = isset($this->ruleNameIndex[ $ruleName ]);

        if ($status) {
            $ruleClass = $this->ruleNameIndex[ $ruleName ];

            return true;
        }

        return false;
    }

    /**
     * @param class-string<RuleInterface> $ruleClass
     */
    public function hasRuleClass(string $ruleClass, ?string &$ruleName = null) : bool
    {
        $ruleName = null;

        $status = isset($this->ruleClassIndex[ $ruleClass ]);

        if ($status) {
            $ruleName = $this->ruleClassIndex[ $ruleClass ];

            return true;
        }

        return false;
    }


    /**
     * @param class-string<RuleInterface> $ruleClass
     */
    public function getRuleName(string $ruleClass) : string
    {
        return $this->ruleClassIndex[ $ruleClass ];
    }

    /**
     * @return class-string<RuleInterface>
     */
    public function getRuleClass(string $ruleName) : string
    {
        return $this->ruleNameIndex[ $ruleName ];
    }


    /**
     * @param class-string<RuleInterface>[] $rules
     *
     * @return static
     */
    public function setRules(array $rules)
    {
        $this->ruleNameIndex = [];
        $this->ruleClassIndex = [];

        foreach ( $rules as $i => $ruleClass ) {
            if (is_string($i)) {
                $ruleClass = $i;
            }

            $this->addRule($ruleClass);
        }

        return $this;
    }

    /**
     * @param class-string<RuleInterface> $ruleClass
     *
     * @return static
     */
    public function addRule(string $ruleClass)
    {
        if (! is_subclass_of($ruleClass, RuleInterface::class, true)) {
            throw new LogicException(
                [
                    'The `ruleClass` should be class-string of: ' . RuleInterface::class,
                    $ruleClass,
                ]
            );
        }

        $ruleName = $ruleClass::NAME;

        if (isset($this->ruleNameIndex[ $ruleName ])) {
            throw new LogicException(
                [ 'The `ruleName` is already registered', $ruleName ]
            );
        }

        $this->ruleNameIndex[ $ruleName ] = $ruleClass;
        $this->ruleClassIndex[ $ruleClass ] = $ruleName;

        return $this;
    }


    /**
     * @param string[] $ruleNames
     *
     * @return static
     */
    public function removeRules(array $ruleNames)
    {
        foreach ( $ruleNames as $i => $ruleName ) {
            if (is_string($i)) {
                $ruleName = $i;
            }

            $this->removeRule($ruleName);
        }

        return $this;
    }

    /**
     * @return static
     */
    public function removeRule(string $ruleName)
    {
        if (! isset($this->ruleNameIndex[ $ruleName ])) {
            return $this;
        }

        $ruleClass = $this->ruleNameIndex[ $ruleName ];

        unset($this->ruleNameIndex[ $ruleName ]);
        unset($this->ruleClassIndex[ $ruleClass ]);

        return $this;
    }
}
